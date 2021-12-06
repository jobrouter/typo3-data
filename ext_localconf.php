<?php
defined('TYPO3') || die();

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Configuration/TypoScript/"'
);
