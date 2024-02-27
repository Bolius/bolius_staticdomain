<?php
declare(strict_types=1);

namespace Bolius\BoliusStaticdomain\Hooks;

use Bolius\BoliusStaticdomain\Service\StaticDomainService;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;

class PageRenderer
{
    /**
     * Called from \TYPO3\CMS\Core\Page\PageRenderer->executePostRenderHook()
     * @param mixed $params
     * @param PageRenderer $pageRenderer
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function postProcess(mixed &$params, PageRenderer $pageRenderer): void
    {
        if (!StaticDomainService::isActive()) {
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
     * @param mixed $params
     * @param PageRenderer $pageRenderer
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function postTransform(mixed &$params, PageRenderer $pageRenderer): void
    {
        if (!StaticDomainService::isActive()) {
            return;
        }

        foreach (['headerData', 'footerData'] as $field) {
            if (!empty($params[$field])) {
                foreach ($params[$field] as $key => &$value) {
                    $value = StaticDomainService::addStaticDomainToAttributesInHtml($value);
                }
            }
        }
    }
}