<?php
if (!defined('TYPO3')) {
    die ('Access denied.');
}

// Add to domain records
$columns = [
    'tx_boliusstaticdomain_static' => [
        'exclude' => 0,
        'label'   => 'Use this domain for static resources (img, js, css etc.)',
        'config'  => [
            'type'    => 'check',
            'default' => 0
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_domain', $columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_domain',
    'tx_boliusstaticdomain_static',
    '',
    ''
);