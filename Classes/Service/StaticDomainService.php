<?php
namespace Bolius\BoliusStaticdomain\Service;

/**
 * Class StaticDomainService
 */
class StaticDomainService
{

    /**
     * @var string
     */
    static $staticDomainName = '';

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
            'replaceDomain' => TRUE,
        ];

        if (!$domain) {
            $domain = self::getStaticDomainName();
        }

        $urlParts = parse_url($url);

        $newUrlparts = $urlParts;

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
                $newUrlparts['host'] = $domain;
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
    public static function getStaticDomainName()
    {
        return 'boliusstatic.local:8000';

        if (empty(self::$staticDomainName)) {
            if ($domainRecord = self::getStaticDomainRecord()) {
                self::$staticDomainName = $domainRecord['domainName'];
            }
        }
        return self::$staticDomainName;
    }

    /**
     * @return array|FALSE|NULL
     */
    public static function getStaticDomainRecord()
    {
        return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            '*',
            'sys_domain',
            'tx_boliusstaticdomain_static=1',
            '',
            'sorting ASC'
        );

    }

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
                ]
            ],
            'source' => [
               'srcset' => [
               ],
            ],
        ];

        $staticDomain = StaticDomainService::getStaticDomainName();

        // find all tags
        if (preg_match_all(';<([^/][^> ]+)[^>]*>;i', $param, $m, PREG_SET_ORDER)) {
            foreach ($m as $ma) {
                $tagName = $ma[1];
                if (! isset($config[$tagName])) {
                    continue;
                }
                if (preg_match_all(';([a-z]+)=("([^"]+)");i', $ma[0], $attributes, PREG_SET_ORDER)) {
                    foreach ($attributes as $attribute) {
                        $attrName = $attribute[1];
                        $attrValue = $attribute[3];

                        if (isset($config[$tagName][$attrName])) {

                            $go = TRUE;

                            if (is_array($config[$tagName]['if-attr-equals'])) {
                                foreach ($config[$tagName]['if-attr-equals'] as $attr => $value) {
                                    if (! preg_match('/' . $attr . '="|\'' . $value . '"|\'', $ma[0])) {
                                     //   $go = FALSE;
                                    }
                                }
                            }

                            if ($go) {
                                $new = self::appendDomainToUrl($attrValue, $staticDomain, $config[$tagName][$attrName]) ;
                                $param = str_replace('"' . $attrValue . '"', '"' . $new . '"', $param);
//                                echo "$tagName:$attrName:$attrValue \n  $new \n";
                            }
                        }
                    }
                }

            }
        }

        return $param;
    }

}