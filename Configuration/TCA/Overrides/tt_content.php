<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

defined('TYPO3') || die();

(static function ($contentType = 'tx_jobrouterdata_table'): void {
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        [
            Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':ce.title',
            $contentType,
            'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/ce-table.svg',
        ],
        'CType',
        Brotkrueml\JobRouterData\Extension::KEY
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$contentType] = 'pi_flexform';
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Configuration/FlexForms/Table.xml',
        $contentType
    );

    $GLOBALS['TCA']['tt_content']['types'][$contentType] = [
        'columnsOverrides' => [
            'pi_flexform' => [
                'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':table',
            ],
        ],
        'previewRenderer' => Brotkrueml\JobRouterData\Preview\ContentElementPreviewRenderer::class,
        'showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
            --div--;' . Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':table,
                pi_flexform,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;;frames,
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
        ',
    ];

    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$contentType] = 'jobrouter-data-ce-table';
})();
