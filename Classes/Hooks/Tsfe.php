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
        $params['pObj']->content = StaticDomainService::addStaticDomainToAttributesInHtml($params['pObj']->content);
    }

}