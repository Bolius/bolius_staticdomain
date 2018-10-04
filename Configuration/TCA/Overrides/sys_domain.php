<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Add to domain records
$columns = array(
    'tx_boliusstaticdomain_static' => array(
        'exclude' => 0,
        'label' => 'Use this domain for static resources (img, js, css etc.)',
        'config' => array(
            'type' => 'check',
            'default' => 0
        )
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_domain', $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_domain', 'tx_boliusstaticdomain_static', '', '');


?>