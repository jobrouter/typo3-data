<?php
defined('TYPO3_MODE') || die('Access denied.');

(function () {
    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Brotkrueml.JobRouterData',
        'jobrouter',
        'jobrouterdata',
        '',
        [
            'Backend' => 'list',
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-module.svg',
            'labels' => 'LLL:EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Private/Language/BackendModule.xlf',
        ]
    );

    $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'jobrouterdata-ce-table',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/ce-table.svg']
    );
    $iconRegistry->registerIcon(
        'jobrouterdata-action-report',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/action-report.svg']
    );
    $iconRegistry->registerIcon(
        'jobrouter-data-toolbar',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-toolbar.svg']
    );

    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Configuration/TSconfig/Page/NewContentElementWizard.tsconfig">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['tx_jobrouterdata_table'] =
        Brotkrueml\JobRouterData\Hooks\PageLayoutView\TablePreviewRenderer::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        Brotkrueml\JobRouterData\Hooks\TableUpdateHook::class;

    if (TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('reports')) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_jobrouterdata']['report'] = [
            'title' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_REPORT . ':title',
            'description' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_REPORT . ':description',
            'icon' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-report.svg',
            'report' => Brotkrueml\JobRouterData\Report\Status::class,
        ];
    }
})();
