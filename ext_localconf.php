<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

/**
 * Configure hooks
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['bolius_staticdomain'] =
    \Bolius\BoliusStaticdomain\Hooks\Tsfe::class . '->contentPostProc_all';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['bolius_staticdomain'] =
    \Bolius\BoliusStaticdomain\Hooks\PageRenderer::class . '->postProcess';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postTransform']['bolius_staticdomain'] =
    \Bolius\BoliusStaticdomain\Hooks\PageRenderer::class . '->postTransform';

/**
 * Configure logger
 */
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Bolius']['BoliusStaticdomain'] = [
    'writerConfiguration' => [
        \Psr\Log\LogLevel::DEBUG => [
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                'logFile' => 'typo3temp/logs/Bolius.BoliusStaticdomain.log'
            ]
        ],
        \Psr\Log\LogLevel::ERROR => [
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                'logFile' => 'typo3temp/logs/Bolius.BoliusStaticdomain.error.log'
            ]
        ],
    ]
];