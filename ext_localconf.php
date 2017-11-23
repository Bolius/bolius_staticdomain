<?php
/**
 *
 *
 */
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorage::SIGNAL_PreGeneratePublicUrl,
    \Bolius\BoliusStaticdomain\Hooks\ResourcePublicUrlGenerator::class,
    'generatePublicUrl'
);


if (TYPO3_MODE == 'FE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['bolius_staticdomain'] =
        \Bolius\BoliusStaticdomain\Hooks\Tsfe::class . '->contentPostProc_all';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['bolius_staticdomain'] =
        \Bolius\BoliusStaticdomain\Hooks\PageRenderer::class . '->postProcess';
}
