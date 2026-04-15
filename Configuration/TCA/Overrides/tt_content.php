<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Data\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'tx_jobrouterdata_table' => [
            'label' => Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':table',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                    ],
                ],
                'foreign_table' => 'tx_jobrouterdata_domain_model_table',
                'foreign_table_where' => 'AND {#tx_jobrouterdata_domain_model_table}.{#type}=1 ORDER BY name',
                'required' => true,
            ],
        ],
    ],
);

ExtensionManagementUtility::addRecordType(
    [
        'label' => Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':ce.title',
        'description' => Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':ce.description',
        'group' => 'special',
        'value' => Extension::CE_TYPE,
        'icon' => 'jobrouter-data-ce-table',
    ],
    '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
        --div--;' . Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':table,
            tx_jobrouterdata_table,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
    ',
);
