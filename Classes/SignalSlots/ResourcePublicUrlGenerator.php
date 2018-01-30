<?php
namespace Bolius\BoliusStaticdomain\Hooks;

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
        return;

        if (! isset($GLOBALS['boliusGeneratingPublicUrl'])) {
            $GLOBALS['boliusGeneratingPublicUrl'] = 1;
            $publicUrl = StaticDomainService::appendDomainToUrl($resourceStorage->getPublicUrl($resourceObject));
            $publicUrl = StaticDomainService::stripAbsRefPrefixFromUrl($publicUrl);
            echo $publicUrl . "\n";
            $urlData['publicUrl'] = $publicUrl;
            unset($GLOBALS['boliusGeneratingPublicUrl']);
        }
    }
}