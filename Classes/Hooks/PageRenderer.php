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
        if (TYPO3_MODE == 'BE') {
            return;
        }

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