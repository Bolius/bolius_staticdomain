<?php
namespace Bolius\BoliusStaticdomain\Hooks;
use Bolius\BoliusStaticdomain\Service\StaticDomainService;


/**
 * Class Tsfe
 */
class Tsfe
{

    /**
     * @param $params
     * @param $tsfe
     */
    public function contentPostProc_all(&$params, $tsfe )
    {
        if (! StaticDomainService::isActive()) {
            return;
        }

        // for now, just content
        $params['pObj']->content = StaticDomainService::addStaticDomainToAttributesInHtml($params['pObj']->content);
    }

}