<?php
namespace Bolius\BoliusStaticdomain\Hooks;
use Bolius\BoliusStaticdomain\Service\StaticDomainService;

/**
 * Class PageRendererPostProcess
 */
class PageRenderer
{

    /**
     * @param $params
     * @param $pageRenderer
     */
    public function postProcess(&$params, $pageRenderer)
    {
        if (! StaticDomainService::isActive()) {
            return;
        }

        foreach ($params as $key => &$param) {
            if (is_string($param)) {
                $param = StaticDomainService::addStaticDomainToAttributesInHtml($param);
            }
        }
    }

    /**
     * @param $params
     */
    public function postTransform($params)
    {

    }

}