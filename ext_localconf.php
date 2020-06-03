<?php
defined('TYPO3_MODE') || die('Access denied.');

(function () {
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '@import "EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Configuration/TypoScript/"'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['jobRouterData'] =
        ['Brotkrueml\\JobRouterData\\ViewHelpers'];

    $configuration = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get(Brotkrueml\JobRouterData\Extension::KEY);

    $writerConfiguration = [];
    if ($configuration['logIntoFile']) {
        $writerConfiguration[TYPO3\CMS\Core\Log\Writer\FileWriter::class] = ['logFileInfix' => Brotkrueml\JobRouterData\Extension::KEY];
    }
    if ($configuration['logIntoTable']) {
        $writerConfiguration[TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class] = ['logTable' => 'tx_jobrouterconnector_log'];
    }

    if (!empty($writerConfiguration)) {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Brotkrueml']['JobRouterData']['writerConfiguration'][$configuration['logLevel']]
            = $writerConfiguration;
    }
})();
