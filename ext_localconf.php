<?php
defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Brotkrueml.JobRouterData',
    'Pi',
    [
        'Table' => 'show',
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:jobrouter_data/Configuration/TypoScript/"'
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['jobRouterData'] =
    ['Brotkrueml\\JobRouterData\\ViewHelpers'];
