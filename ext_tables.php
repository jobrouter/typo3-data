<?php

use Brotkrueml\JobRouterData\Controller\TableListController;
use Brotkrueml\JobRouterData\Extension;
use Brotkrueml\JobRouterData\Hooks\PageLayoutView\JobDataTablePreviewRenderer;
use Brotkrueml\JobRouterData\Hooks\TableUpdateHook;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addModule(
    'jobrouter',
    'data',
    '',
    '',
    [
        'routeTarget' => TableListController::class . '::handleRequest',
        'access' => 'admin',
        'name' => Extension::MODULE_NAME,
        'iconIdentifier' => 'jobrouter-module-data',
        'labels' => Extension::LANGUAGE_PATH_BACKEND_MODULE,
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
