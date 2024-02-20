<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;
use JobRouter\AddOn\Typo3Data\Extension;
use JobRouter\AddOn\Typo3Data\UserFunctions\TCA\Column;

return [
    'ctrl' => [
        'title' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column',
        'label' => 'name',
        'label_userFunc' => Column::class . '->getLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'rootLevel' => 1,
        'searchFields' => 'name,label',
        'iconfile' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_column.svg',
        'hideTable' => true,
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'name' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 20,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'label' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'type' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . FieldType::Text->value,
                        FieldType::Text->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . FieldType::Integer->value,
                        FieldType::Integer->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . FieldType::Decimal->value,
                        FieldType::Decimal->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . FieldType::Date->value,
                        FieldType::Date->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . FieldType::DateTime->value,
                        FieldType::DateTime->value,
                    ],
                ],
                'required' => true,
            ],
        ],
        'decimal_places' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.decimal_places',
            'displayCond' => 'FIELD:type:=:' . FieldType::Decimal->value,
            'config' => [
                'type' => 'number',
                'size' => 3,
                'range' => [
                    'lower' => 1,
                    'upper' => 10,
                ],
                'slider' => [
                    'step' => 1,
                    'width' => 200,
                ],
                'default' => 2,
            ],
        ],
        'field_size' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.field_size',
            'displayCond' => [
                'OR' => [
                    'REC:NEW:true',
                    'FIELD:type:=:' . FieldType::Text->value,
                ],
            ],
            'config' => [
                'type' => 'number',
                'size' => 5,
                'max' => 5,
                'range' => [
                    'lower' => 0,
                ],
                'default' => 0,
            ],
        ],
        'alignment' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        '',
                    ],
                    [
                        Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment.left',
                        'left',
                    ],
                    [
                        Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment.center',
                        'center',
                    ],
                    [
                        Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment.right',
                        'right',
                    ],
                ],
            ],
        ],
        'sorting_priority' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_priority',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0,
                    ],
                    [
                        '1',
                        1,
                    ],
                    [
                        '2',
                        2,
                    ],
                    [
                        '3',
                        3,
                    ],
                ],
            ],
        ],
        'sorting_order' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_order',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        '',
                    ],
                    [
                        Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_order.asc',
                        'asc',
                    ],
                    [
                        Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_order.desc',
                        'desc',
                    ],
                ],
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameLabel,
                --palette--;;type,
            ',
        ],
    ],
    'palettes' => [
        'nameLabel' => [
            'showitem' => 'name, label',
        ],
        'type' => [
            'showitem' => 'type, decimal_places, field_size',
        ],
        'rendering' => [
            // Palette is used in columnOverrides of tx_jobrouterdata_domain_model_column for simple table type
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':palette.rendering_ce',
            'showitem' => 'alignment, sorting_priority, sorting_order',
        ],
    ],
];
