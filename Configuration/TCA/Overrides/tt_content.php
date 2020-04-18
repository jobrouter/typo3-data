<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

defined('TYPO3_MODE') || die();

(function ($extensionKey='jobrouter_data', $contentType='tx_jobrouterdata_table') {
    $llPrefix = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/ContentElement.xlf:';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        [
            $llPrefix . 'ce.title',
            $contentType,
            'EXT:' . $extensionKey . '/Resources/Public/Icons/ce-table.svg',
        ],
        'CType',
        $extensionKey
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$contentType] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:' . $extensionKey . '/Configuration/FlexForms/Table.xml',
        $contentType
    );

    $tempTypes = [
        $contentType => [
            'columnsOverrides' => [
                'pi_flexform' => [
                    'label' => $llPrefix . 'table',
                ]
            ],
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;general,
                    --palette--;;headers,
                --div--;' . $llPrefix . 'table,
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
        ],
    ];

    $GLOBALS['TCA']['tt_content']['types'] += $tempTypes;

    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$contentType] = 'jobrouterdata-ce-table';
})();
