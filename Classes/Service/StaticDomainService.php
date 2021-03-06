<?php
namespace Bolius\BoliusStaticdomain\Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StaticDomainService
 */
class StaticDomainService
{

    /**
     * @var string
     */
    static $staticDomainNames = [];

    /**
     * Appends/replaces the static domain name to/in any url :
     * - a relative url (typically used in TYPO3 backend, like "sysext/backend/....")
     * - an absolute url without host (like "/typo3temp/....")
     * - an absolute url with host but without scheme (like "//example.com/foo/...")
     * - an absolute url with host and scheme (like "http://example.com/foo/...")
     *
     * @param $url
     * @param null $domain
     * @param bool $evenIfUrlHasHost
     * @param null $forceScheme If set, will replace the scheme from the url with this. Valid are "http", "https", "" or NULL.
     * @return string
     */
    public static function appendDomainToUrl($url, $domain = NULL, $config = [])
    {
        $config += [
            'addDomain' => TRUE,
            'replaceDomain' => FALSE,
        ];

        if (!$domain) {
            $domain = self::getStaticDomainName($GLOBALS['TSFE']->id);
        }

        if ($domain) {

            $urlParts = parse_url($url);

            $newUrlparts = $urlParts;

            if (empty($newUrlparts['scheme']) || preg_match('/http/i', $newUrlparts['scheme'])) {

                /**
                 * Add domain ?
                 */
                if ($config['addDomain']) {
                    if (empty($urlParts['host'])) {
                        $newUrlparts['host'] = $domain;
                    }
                }

                /**
                 * Replace domain ? - not now
                 */
                if ($config['replaceDomain']) {
                    if (! empty($urlParts['host'])) {
    //                    $newUrlparts['host'] = $domain;
                    }
                }

                $url = implode('', [

                    empty($newUrlparts['scheme']) ? '' : ($newUrlparts['scheme'] . ':'),
                    '//',
                    empty($newUrlparts['host']) ? '' : $newUrlparts['host'],
                    '/' . ltrim($newUrlparts['path'], '/'),
                    empty($newUrlparts['query']) ? '' : ('?' . $newUrlparts['query']),
                    empty($newUrlparts['fragment']) ? '' : ('#' . $newUrlparts['fragment']),


                ]);
            }
        }

        return $url;
    }


    /**
     * Strip wrongly prepended absRefPre
     */
    public static function stripAbsRefPrefixFromUrl ($s)
    {
        // An extra slash is added somewhere later. To compensate, we remove one here.
        if (substr($s, 0, 2) == '//') {
            $s = substr($s, 1);
        }
        return $s;
    }

    /**
     * @return bool
     */
    public static function getStaticDomainName($targetPid)
    {
        if (empty(self::$staticDomainNames[$targetPid])) {
            if ($domainRecord = self::getStaticDomainRecord($targetPid)) {
                self::$staticDomainNames[$targetPid] = $domainRecord['domainName'];
            }
        }
        return self::$staticDomainNames[$targetPid];
    }

    /**
     * @return array|FALSE|NULL
     */
    public static function getStaticDomainRecord($targetPid)
    {
        $rootline = $GLOBALS['TSFE']->sys_page->getRootLine($targetPid);

        foreach ($rootline as $pageInRootline) {
            $pidInRootline = $pageInRootline['uid'];
            if (class_exists('TYPO3\CMS\Core\Database\Query\QueryBuilder')) {
                // v 9.x
                /** @var object $queryBuilder */
                $queryBuilder = GeneralUtility::makeInstance('TYPO3\CMS\Core\Database\ConnectionPool')->getQueryBuilderForTable('sys_domain');
                $res = $queryBuilder->select('*')
                    ->from('sys_domain')
                    ->where(
                        $queryBuilder->expr()->eq('pid', $pidInRootline),
                        $queryBuilder->expr()->eq('tx_boliusstaticdomain_static', 1),
                        $queryBuilder->expr()->eq('hidden', 0)
                    )
                    ->orderBy('sorting')
                    ->execute()
                    ->fetch();

                if ($res) {
                    return $res;
                }
            } else {
                // v 7.x
                $domain = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
                    '*',
                    'sys_domain',
                    'tx_boliusstaticdomain_static=1 and not hidden and pid=' . intval($pidInRootline),
                    '',
                    'sorting ASC'
                );
                if ($domain) {
                    return $domain;
                }
            }
        }

        return FALSE;

    }

    /**
     * @param $param
     * @return mixed
     */
    public static function addStaticDomainToAttributesInHtml ($param)
    {
        $config = [
            'img' => [
                'src' => [
                ]
            ],
            'link' => [
                'href' => [
                ]
            ],
            'script' => [
                'src' => [
                    'addCrossoriginToTag' => 1,
                ],
            ],
            'source' => [
                'src' => [
                ],
                'srcset' => [

                ],
            ],
        ];

        $staticDomain = StaticDomainService::getStaticDomainName($GLOBALS['TSFE']->id);
        // find all tags
        if (preg_match_all(';<([^/][^> ]+)[^>]*>;i', $param, $m, PREG_SET_ORDER)) {
            foreach ($m as $ma) {

                $fullTag = $ma[0];
                $tagName = $ma[1];
                if (! isset($config[$tagName])) {
                    continue;
                }
                // not sure if colon is allowed in attribute name, but vue.js uses it
                if (preg_match_all(';\W([a-z:]+)=("([^"]+)");i', $ma[0], $attributes, PREG_SET_ORDER)) {
                    foreach ($attributes as $attribute) {
                        $attrName = $attribute[1];
                        $attrValue = $attribute[3];

                        if (isset($config[$tagName][$attrName])) {

                            $new = $attrValue;
                            if ($staticDomain) {
                                $new = self::appendDomainToUrl($attrValue, $staticDomain, $config[$tagName][$attrName]) ;
                            }

                            $changed = $new != $attrValue;

                            $newQuoted = '"' . $new . '"';

                            if (
                                // configuration says we should add crossorigin
                                ! empty($config[$tagName][$attrName]['addCrossoriginToTag'])
                                // url has a hostname
                                && preg_match(';^(https?:)?//;', $new)
                                // url does not contain the currently requested hostname
                                && $changed
                                // tag does not already contain crossorigin
                                && !stristr($fullTag, 'crossorigin'))
                            {
                                $newQuoted .= ' crossorigin';
                            }

                            $newFullTag = str_replace('"' . $attrValue . '"', $newQuoted, $fullTag);
                            $param = str_replace($fullTag, $newFullTag, $param);

                            if (FALSE) {
                                print_R([
                                    'fullTag' =>$fullTag,
                                    'attrValue' => $attrValue,
                                    'newUrl' => $new,
                                    'newFullTag' => $newFullTag,
                                ]);
                            }

                        }
                    }

                }
            }
        }

        return $param;
    }

    /**
     * Should static domain functionality be activated in this request ?
     *
     * @return bool
     */
    public static function isActive ()
    {
        // this extension does not work in backend - yet
        if (TYPO3_MODE == 'BE') {
            return FALSE;
        }

        $extConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['bolius_staticdomain'];
        if ($extConf) {
            $extConf = unserialize($extConf);

            if (! empty($extConf['disable'])) {
                return FALSE;
            }

            // TYPO3 user is logged in - maybe move this to adminPanel ?
            if (! empty($extConf['disable_with_be_login']) && ! empty($GLOBALS['BE_USER'])) {
                return FALSE;
            }

            // this is where all your visitors are
            if (! empty($extConf['disable_without_be_login']) && empty($GLOBALS['BE_USER'])) {
                return FALSE;
            }
            
            // exact match on hostnames
            if (! empty($extConf['disable_on_hostnames'])) {
                foreach (explode(',', $extConf['disable_on_hostnames']) as $hostname) {
                    if ($_SERVER['HTTP_HOST'] == trim($hostname)) {
                        return FALSE;
                    }
                }
            }
        }

        // if static domain needs to be deactivated for some reason, add rules here
        // Could be ip-address, TypoScript, cookies, get parameters etc.

        return TRUE;
    }
}
