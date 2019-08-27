<?php
defined('TYPO3_MODE') || die('Access denied.');

(function ($extensionKey) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Brotkrueml.JobRouterData',
        'jobrouter',
        'jobrouterdata',
        '',
        [
            'Backend' => 'list',
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/jobrouter-data-module.svg',
            'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/BackendModule.xlf',
        ]
    );
})('jobrouter_data');
