<?php
defined('TYPO3_MODE') || die('Access denied.');

(function ($extensionKey = 'jobrouter_data') {
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

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'jobrouterdata-ce-table',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/ce-table.svg']
    );
    $iconRegistry->registerIcon(
        'jobrouterdata-action-report',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/action-report.svg']
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extensionKey . '/Configuration/TSconfig/Page/NewContentElementWizard.tsconfig">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['tx_jobrouterdata_table'] =
        \Brotkrueml\JobRouterData\Hooks\PageLayoutView\TablePreviewRenderer::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        \Brotkrueml\JobRouterData\Hooks\TableUpdateHook::class;

    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('reports')) {
        $reportL10nPrefix = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/Report.xlf:';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_jobrouterdata']['report'] = [
            'title'       => $reportL10nPrefix . 'title',
            'description' => $reportL10nPrefix . 'description',
            'icon'        => 'EXT:' . $extensionKey . '/Resources/Public/Icons/jobrouter-data-report.svg',
            'report'      => \Brotkrueml\JobRouterData\Report\Status::class,
        ];
    }
})();
