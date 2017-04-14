<?php
namespace Bolius\BoliusStaticdomain\Service;

/**
 * Class StaticDomainService
 */
class StaticDomainService
{

    /**
     * Appends the static domain name to a relative url.
     *
     * @param $url
     * @param null $domain
     * @param bool $evenIfUrlHasHost
     * @param null $forceScheme
     * @return string
     */
    public static function appendDomainToUrl($url, $domain = NULL, $evenIfUrlHasHost = FALSE, $forceScheme = NULL)
    {

        if (!$domain) {
            $domain = self::getStaticDomainName();
        }

        $urlParts = parse_url($url);

        $url = '';
        if ($forceScheme !== NULL) {
            $trimmedScheme = trim($forceScheme, ':');
            $url .= $trimmedScheme;
            if ($trimmedScheme) {
                $url .= ':';
            }
        } else {
            if (!empty($urlParts['scheme'])) {
                $url .= trim($urlParts['scheme'], ':') . ':';
            }

        }

        // @TODO : not quite sure this logic is correct. Or in correct place.
        if ($GLOBALS['TSFE']->absRefPrefix == '/') {
            $url .= '/';
        } else {
            $url .= '//';
        }

        if (empty($urlParts['host']) || $evenIfUrlHasHost) {
            $url .= $domain;
        } else {
            $url .= $urlParts['host'];
        }

        $path = $_SERVER['REQUEST_URI'];
        if (preg_match(';/typo3(/|$);', $path)) {
            $dirname = 'typo3';
        } else {
            $dirname = dirname(parse_url($path)['path']);
        }

        //$url .=  '/' . $dirname . '/' . ltrim($urlParts['path'], '/');
        $url .=  '/' . ltrim($urlParts['path'], '/');

        if (!empty($urlParts['query'])) {
            $url .= '?' . $urlParts['query'];
        }
        if (!empty($urlParts['fragment'])) {
            $url .= '#' . $urlParts['fragment'];
        }

        return $url;
    }


    /**
     * Strip wrongly prepended absRefPre
     */
    public static function stripAbsRefPrefixFromUrl ($s)
    {
        return $s;
    }

    /**
     * @return bool
     */
    public static function getStaticDomainName()
    {
        if ($domainRecord = self::getStaticDomainRecord()) {
            return $domainRecord['domainName'];
        }
        return FALSE;
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

        $staticDomain = StaticDomainService::getStaticDomainName();

        $path = $_SERVER['REQUEST_URI'];
        if (preg_match(';/typo3(/|$);', $path)) {
            $dirname = 'typo3';
        } else {
            $dirname = dirname(parse_url($path)['path']);
        }


        // @TODO : this will fail if a url with host is passed in
        if (preg_match_all(';((href)|(src))=("(/?)([^"]+)");i', $param, $m, PREG_SET_ORDER)) {
            foreach ($m as $ma) {
                if ($ma{5} == '/') {
                    // absolute
                    $param = str_replace($ma[4], '"//' . $staticDomain . '/' . trim($dirname, '/') . '/' . $ma[6] . '"', $param);
                } else {
                    // relative
                    $param = str_replace($ma[4], '"//' . $staticDomain . '/' . trim($dirname, '/') . '/' . $ma[6] . '"', $param);
                }


            }
        }
//                $param = preg_replace(';href="(/?)([^"]+)";i', 'href="//' . $staticDomain . '/' . trim($dirname, '/') . '/\\2"', $param);

        return $param;
    }

}