<?php
declare(strict_types=1);

namespace Bolius\BoliusStaticdomain\Service;

use TYPO3\CMS\Core\Utility\RootlineUtility;
use Bolius\BoliusStaticdomain\Classes\Domain\Repository\SysDomainRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Log\LogManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StaticDomainService
{
    static string|array $staticDomainNames = [];

    protected static ?LoggerInterface  $logger                 = null;
    protected static array|string|null $extensionConfiguration = null;

    /**
     * Appends/replaces the static domain name to/in any url :
     * - a relative url (typically used in TYPO3 backend, like "sysext/backend/....")
     * - an absolute url without host (like "/typo3temp/....")
     * - an absolute url with host but without scheme (like "//example.com/foo/...")
     * - an absolute url with host and scheme (like "http://example.com/foo/...")
     * @param $url
     * @param mixed $domain
     * @param array $config
     * @return string
     */
    public static function appendDomainToUrl($url, mixed $domain = null, array $config = []): string
    {
        $config += [
            'addDomain'     => true,
            'replaceDomain' => false,
        ];

        $domain = $domain ?: self::getStaticDomainName($GLOBALS['TSFE']->id);

        if (!$domain) {
            return $url;
        }

        $urlParts = parse_url($url);
        $newUrlParts = $urlParts;

        if (!empty($newUrlParts['scheme']) && !preg_match('/http/i', $newUrlParts['scheme'])) {
            return $url;
        }

        if ($config['addDomain']) {
            if (empty($urlParts['host'])) {
                $newUrlParts['host'] = $domain;
            }
        }

        return sprintf(
            '%s//%s/%s%s%s',
            empty($newUrlParts['scheme']) ? '' : ($newUrlParts['scheme'] . ':'),
            empty($newUrlParts['host']) ? '' : $newUrlParts['host'],
            ltrim($newUrlParts['path'], '/'),
            empty($newUrlParts['query']) ? '' : ('?' . $newUrlParts['query']),
            empty($newUrlParts['fragment']) ? '' : ('#' . $newUrlParts['fragment'])
        );
    }


    /**
     * Strip wrongly prepended absRefPre
     */
    public static function stripAbsRefPrefixFromUrl(string $string): string
    {
        // An extra slash is added somewhere later. To compensate, we remove one here.
        if (str_starts_with($string, '//')) {
            $string = substr($string, 1);
        }

        return $string;
    }

    /**
     * @param string|int $targetPid
     * @return bool|string
     */
    public static function getStaticDomainName(string|int $targetPid): bool|string
    {
        if (empty(self::$staticDomainNames[$targetPid])) {
            if ($domainRecord = self::getStaticDomainRecord($targetPid)) {
                self::$staticDomainNames[$targetPid] = $domainRecord['domainName'];
            }
        }

        return self::$staticDomainNames[$targetPid];
    }

    /**
     * Returns a record array or:
     *  - false - when there has been an error retrieving the record.
     *  - null - if no record has been found.
     * @param int $targetPid
     * @return array|null|false
     */
    public static function getStaticDomainRecord(int $targetPid): array|null|false
    {
        $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, $targetPid)->get();

        if (empty($rootLine)) {
            return null;
        }

        try {
            /** @var SysDomainRepository $sysDomainRepository */
            $sysDomainRepository = GeneralUtility::makeInstance(SysDomainRepository::class);

            foreach ($rootLine as $pageInRootLine) {
                $result = $sysDomainRepository->getRecordByPid($pageInRootLine['uid']);

                if ($result) {
                    return $result;
                }
            }
        } catch (Exception|DBALException $e) {
            self::getStaticLogger()->error($e->getMessage());

            return false;
        }

        return null;
    }

    /**
     * @param string $param
     * @return string|array
     */
    public static function addStaticDomainToAttributesInHtml(string $param): string|array
    {
        if (!preg_match_all(';<([^/][^> ]+)[^>]*>;i', $param, $matches, PREG_SET_ORDER)) {
            return $param;
        }

        $config = self::getConfig();
        $staticDomain = self::getStaticDomainName($GLOBALS['TSFE']->id);

        // find all tags
        foreach ($matches as $match) {
            $fullTag = $match[0];
            $tagName = $match[1];

            if (!isset($config[$tagName])) {
                continue;
            }

            // not sure if colon is allowed in attribute name, but vue.js uses it
            if (!preg_match_all(';\W([a-z:]+)=("([^"]+)");i', $match[0], $attributes, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($attributes as $attribute) {
                $attrName = $attribute[1];
                $attrValue = $attribute[3];

                if (!isset($config[$tagName][$attrName])) {
                    continue;
                }

                $new = $attrValue;
                if ($staticDomain) {
                    $new = self::appendDomainToUrl($attrValue, $staticDomain, $config[$tagName][$attrName]);
                }

                $changed = $new != $attrValue;

                $newQuoted = '"' . $new . '"';

                if (
                    // configuration says we should add crossorigin
                    !empty($config[$tagName][$attrName]['addCrossoriginToTag'])
                    // url has a hostname
                    && preg_match(';^(https?:)?//;', $new)
                    // url does not contain the currently requested hostname
                    && $changed
                    // tag does not already contain crossorigin
                    && !stristr($fullTag, 'crossorigin')
                ) {
                    $newQuoted .= ' crossorigin';
                }

                $newFullTag = str_replace('"' . $attrValue . '"', $newQuoted, $fullTag);
                $param = str_replace($fullTag, $newFullTag, $param);
            }
        }

        return $param;
    }

    /**
     * Should static domain functionality be activated in this request?
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isActive(): bool
    {
        // this extension does not work in backend - yet
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            return false;
        }

        $extConf = self::getExtensionConfiguration();

        if (empty($extConf)) {
            return true;
        }

        if (!empty($extConf['disable'])) {
            return false;
        }

        // TYPO3 user is logged in - maybe move this to adminPanel ?
        if (!empty($extConf['disable_with_be_login']) && !empty($GLOBALS['BE_USER'])) {
            return false;
        }

        // this is where all your visitors are
        if (!empty($extConf['disable_without_be_login']) && empty($GLOBALS['BE_USER'])) {
            return false;
        }

        // exact match on hostnames
        if (!empty($extConf['disable_on_hostnames'])) {
            foreach (explode(',', $extConf['disable_on_hostnames']) as $hostname) {
                if ($_SERVER['HTTP_HOST'] == trim($hostname)) {
                    return false;
                }
            }
        }

        // if static domain needs to be deactivated for some reason, add rules here
        // Could be ip-address, TypoScript, cookies, get parameters etc.

        return true;
    }

    protected static function getStaticLogger(): LoggerInterface
    {
        if (!self::$logger) {
            /** @var LogManagerInterface $logManager */
            $logManager = GeneralUtility::makeInstance(LogManagerInterface::class);

            self::$logger = $logManager->getLogger('Bolius.BoliusStaticdomain');
        }

        return self::$logger;
    }

    private static function getConfig(): array
    {
        return [
            'img'    => [
                'src' => [
                ]
            ],
            'link'   => [
                'href' => [
                ]
            ],
            'script' => [
                'src' => [
                    'addCrossoriginToTag' => 1,
                ],
            ],
            'source' => [
                'src'    => [
                ],
                'srcset' => [

                ],
            ],
        ];
    }

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    private static function getExtensionConfiguration(): array
    {
        if (empty(self::$extensionConfiguration)) {
            $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

            self::$extensionConfiguration = $extensionConfiguration->get('bolius_staticdomain');
        }

        return is_array(self::$extensionConfiguration)
            ? self::$extensionConfiguration
            : unserialize(self::$extensionConfiguration ?? '');
    }
}