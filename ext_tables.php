<?php

use Brotkrueml\JobRouterData\Controller\BackendController;
use Brotkrueml\JobRouterData\Extension;
use Brotkrueml\JobRouterData\Hooks\PageLayoutView\JobDataTablePreviewRenderer;
use Brotkrueml\JobRouterData\Hooks\TableUpdateHook;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

ExtensionUtility::registerModule(
    'JobRouterData',
    'jobrouter',
    'tables',
    '',
    [
        BackendController::class => 'list',
    ],
    [
        'access' => 'admin',
        'iconIdentifier' => 'jobrouter-module-data',
        'labels' => 'LLL:EXT:' . Extension::KEY . '/Resources/Private/Language/BackendModule.xlf',
        'workspaces' => 'online',
    ]
);

ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . Extension::KEY . '/Configuration/TSconfig/Page/NewContentElementWizard.tsconfig">'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['tx_jobrouterdata_table'] =
    JobDataTablePreviewRenderer::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
    TableUpdateHook::class;
