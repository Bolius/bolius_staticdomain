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
        foreach ($params as &$param) {
            if (is_string($param)) {
                $param = StaticDomainService::addStaticDomainToAttributesInHtml($param);

            }
        }

    }

    public function postTransform($params)
    {

    }

}