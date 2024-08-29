<?php

use JobRouter\AddOn\Typo3Data\Controller\TableListController;
use JobRouter\AddOn\Typo3Data\Extension;
use JobRouter\AddOn\Typo3Data\Hooks\TableUpdateHook;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
    TableUpdateHook::class;
