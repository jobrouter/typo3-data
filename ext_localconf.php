<?php

use JobRouter\AddOn\Typo3Data\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:' . Extension::KEY . '/Configuration/TypoScript/"'
);
