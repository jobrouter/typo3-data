<?php

use Brotkrueml\JobRouterData\Controller\TableListController;
use Brotkrueml\JobRouterData\Extension;
use Brotkrueml\JobRouterData\Hooks\TableUpdateHook;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

if ((new Typo3Version())->getMajorVersion() === 11) {
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
        '@import "EXT:' . Extension::KEY . '/Configuration/page.tsconfig"'
    );
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
    TableUpdateHook::class;
