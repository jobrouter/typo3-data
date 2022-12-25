<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Configuration/TypoScript/"'
);
