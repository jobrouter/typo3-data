<?php

use JobRouter\AddOn\Typo3Data\Hooks\TableUpdateHook;

defined('TYPO3') || die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
    TableUpdateHook::class;
