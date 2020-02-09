<?php
defined('TYPO3_MODE') || die('Access denied.');

(function ($extensionKey = 'jobrouter_data') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '@import "EXT:' . $extensionKey . '/Configuration/TypoScript/"'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['jobRouterData'] =
        ['Brotkrueml\\JobRouterData\\ViewHelpers'];

    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('jobrouter_data');

    $writerConfiguration = [];
    if ($configuration['logIntoFile']) {
        $writerConfiguration[\TYPO3\CMS\Core\Log\Writer\FileWriter::class] = ['logFileInfix' => $extensionKey];
    }
    if ($configuration['logIntoTable']) {
        $writerConfiguration[\TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class] = ['logTable' => 'tx_jobrouterconnector_log'];
    }

    if (!empty($writerConfiguration)) {
        $logLevel = (int)$configuration['logLevel'];
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Brotkrueml']['JobRouterData']['writerConfiguration'][$logLevel]
            = $writerConfiguration;
    }
})();
