<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Data\Enumerations\TableType;
use JobRouter\AddOn\Typo3Data\Extension;
use JobRouter\AddOn\Typo3Data\UserFunctions\FormEngine\CustomTables;

return [
    'ctrl' => [
        'title' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table',
        'label' => 'name',
        'descriptionColumn' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'type' => 'type',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'handle,name,table_guid,description',
        'iconfile' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_table.svg',
        'hideTable' => true,
    ],
    'columns' => [
        'disabled' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],

        'type' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.simple_synchronisation',
                        'value' => TableType::Simple->value,
                    ],
                    [
                        'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.synchronisation_in_custom_table',
                        'value' => TableType::CustomTable->value,
                    ],
                    [
                        'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.form_finisher',
                        'value' => TableType::FormFinisher->value,
                    ],
                    [
                        'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.other_usage',
                        'value' => TableType::OtherUsage->value,
                    ],
                ],
            ],
        ],
        'connection' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.connection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_jobrouterconnector_domain_model_connection',
                'foreign_table_where' => ' ORDER BY tx_jobrouterconnector_domain_model_connection.name',
                'eval' => 'int',
                'required' => true,
            ],
        ],
        'handle' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
                'eval' => 'alphanum_x,trim,unique',
                'required' => 'true',
            ],
        ],
        'name' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'table_guid' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.table_guid',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 36,
                'eval' => 'alphanum_x,trim,upper',
                'required' => true,
            ],
        ],
        'custom_table' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.custom_table',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                    ],
                ],
                'itemsProcFunc' => CustomTables::class . '->getTables',
                'required' => true,
            ],
        ],
        'columns' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.columns',
            'config' => [
                'type' => 'inline',
                'allowed' => 'tx_jobrouterdata_domain_model_column',
                'foreign_table' => 'tx_jobrouterdata_domain_model_column',
                'foreign_sortby' => 'sorting',
                'foreign_field' => 'table_uid',
                'minitems' => 1,
                'maxitems' => 100,
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                    'levelLinksPosition' => 'bottom',
                    'useSortable' => true,
                    'enabledControls' => [
                        'info' => false,
                    ],
                ],
            ],
        ],
        'last_sync_date' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.last_sync_date',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'last_sync_error' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.last_sync_error',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'readOnly' => true,
            ],
        ],
        'description' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.description',
            'config' => [
                'type' => 'text',
                'rows' => 5,
                'cols' => 30,
            ],
        ],
    ],
    'types' => [
        (string) TableType::Simple->value => [
            'columnsOverrides' => [
                'columns' => [
                    'config' => [
                        'overrideChildTca' => [
                            'types' => [
                                '0' => [
                                    'showitem' => '
                                        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                                        --palette--;;nameLabel,
                                        --palette--;;type,
                                        --palette--;;rendering,
                                    ',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'showitem' => '
                type, connection, name, handle, table_guid, columns,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;' . Extension::LANGUAGE_PATH_DATABASE . ':tab.status,
                --palette--;;synchronisationStatus,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
            ',
        ],
        (string) TableType::CustomTable->value => [
            'showitem' => '
                type, connection, name, handle, table_guid, custom_table,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;' . Extension::LANGUAGE_PATH_DATABASE . ':tab.status,
                --palette--;;synchronisationStatus,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
            ',
        ],
        (string) TableType::FormFinisher->value => [
            'showitem' => '
                type, connection, name, handle, table_guid, columns,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
            ',
        ],
        (string) TableType::OtherUsage->value => [
            'showitem' => '
                type, connection, name, handle, table_guid,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
            ',
        ],
    ],
    'palettes' => [
        'synchronisationStatus' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':palette.last_synchronisation',
            'showitem' => 'last_sync_date, --linebreak--, last_sync_error',
        ],
    ],
];
