<?php
namespace Bolius\BoliusBoliusdk\Hooks;

use Bolius\BoliusStaticdomain\Service\StaticDomainService;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\ResourceInterface;
use TYPO3\CMS\Core\Resource\ResourceStorage;


/**
 * Class ResourcePublicUrlGenerator
 */
class ResourcePublicUrlGenerator
{

    /**
     * @param ResourceStorage $resourceStorage
     * @param DriverInterface $driver
     * @param ResourceInterface $resourceObject
     * @param $relativeToCurrentScript
     * @param $urlData
     */
    public function generatePublicUrl (ResourceStorage $resourceStorage, DriverInterface $driver, ResourceInterface $resourceObject, $relativeToCurrentScript, $urlData)
    {
        if (! isset($GLOBALS['boliusGeneratingPublicUrl'])) {
            $GLOBALS['boliusGeneratingPublicUrl'] = 1;
            $publicUrl = StaticDomainService::appendDomainToUrl($resourceStorage->getPublicUrl($resourceObject));
            $publicUrl = StaticDomainService::stripAbsRefPrefixFromUrl($publicUrl);
            $urlData['publicUrl'] = $publicUrl;
            unset($GLOBALS['boliusGeneratingPublicUrl']);
        }
    }
}