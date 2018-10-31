<?php
namespace Bolius\BoliusStaticdomain\Hooks;
use Bolius\BoliusStaticdomain\Service\StaticDomainService;

/**
 * Class PageRendererPostProcess
 */
class PageRenderer
{

    /**
     * Called from \TYPO3\CMS\Core\Page\PageRenderer->executePostRenderHook()
     *
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
     * Called from \TYPO3\CMS\Core\Page\PageRenderer->executeRenderPostTransformHook()
     *
     * @param $params
     */
    public function postTransform(&$params, $pageRenderer)
    {
        if (! StaticDomainService::isActive()) {
            return;
        }

        foreach (['headerData', 'footerData'] as $field) {
            if (! empty($params[$field])) {
                foreach ($params[$field] as $k => &$v) {
                    $v = StaticDomainService::addStaticDomainToAttributesInHtml($v);
                }
            }
        }
    }

}