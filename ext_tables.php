<?php
defined('TYPO3') || die();

(static function () {
    if ((new TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() === 10) {
        // Since TYPO3 v11.4 icons can be registered in Configuration/Icons.php
        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
        $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'jobrouter-module-data',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-module.svg',
            ]
        );
        $iconRegistry->registerIcon(
            'jobrouter-data-ce-table',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/ce-table.svg']
        );
        $iconRegistry->registerIcon(
            'jobrouter-data-toolbar',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-toolbar.svg']
        );
    }

    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'JobRouterData',
        'jobrouter',
        'jobrouterdata',
        '',
        [
            Brotkrueml\JobRouterData\Controller\BackendController::class => 'list',
        ],
        [
            'access' => 'admin',
            'iconIdentifier' => 'jobrouter-module-data',
            'labels' => 'LLL:EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Private/Language/BackendModule.xlf',
        ]
    );

    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Configuration/TSconfig/Page/NewContentElementWizard.tsconfig">'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['tx_jobrouterdata_table'] =
        Brotkrueml\JobRouterData\Hooks\PageLayoutView\TablePreviewRenderer::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        Brotkrueml\JobRouterData\Hooks\TableUpdateHook::class;
})();
