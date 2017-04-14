<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Add to domain records
$columns = array(
    'tx_boliusstaticdomain_static' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:bolius_staticdomain/Resources/Private/Language/da.locallang_db.xlf:tx_boliusstaticdomain_static.label',
        'config' => array(
            'type' => 'check',
            'default' => 0
        )
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_domain', $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_domain', 'tx_boliusstaticdomain_static', '', '');


?>