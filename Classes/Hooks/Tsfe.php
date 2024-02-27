<?php
declare(strict_types=1);

namespace Bolius\BoliusStaticdomain\Hooks;

use Bolius\BoliusStaticdomain\Service\StaticDomainService;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class Tsfe
{
    /**
     * Called from \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController->generatePage_postProcessing()
     * @param mixed $params
     * @param TypoScriptFrontendController $tsfe
     * @return void
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function contentPostProc_all(mixed &$params, TypoScriptFrontendController $tsfe): void
    {
        if (!StaticDomainService::isActive()) {
            return;
        }

        $params['pObj']->content = StaticDomainService::addStaticDomainToAttributesInHtml($params['pObj']->content);
    }
}