<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Data\Extension;
use JobRouter\AddOn\Typo3Data\Preview\ContentElementPreviewRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

(static function ($contentType = 'tx_jobrouterdata_table'): void {
    ExtensionManagementUtility::addPlugin(
        [
            Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':ce.title',
            $contentType,
            'EXT:' . Extension::KEY . '/Resources/Public/Icons/ce-table.svg',
        ],
        'CType',
        Extension::KEY,
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$contentType] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:' . Extension::KEY . '/Configuration/FlexForms/Table.xml',
        $contentType,
    );

    $GLOBALS['TCA']['tt_content']['types'][$contentType] = [
        'columnsOverrides' => [
            'pi_flexform' => [
                'label' => Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':table',
            ],
        ],
        'previewRenderer' => ContentElementPreviewRenderer::class,
        'showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
            --div--;' . Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':table,
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
