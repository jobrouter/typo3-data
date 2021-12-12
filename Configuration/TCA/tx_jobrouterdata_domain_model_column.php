<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column',
        'label' => 'label',
        'label_alt' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'rootLevel' => 1,
        'searchFields' => 'name,label',
        'iconfile' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_column.svg',
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
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 20,
                'eval' => 'required,trim',
            ],
        ],
        'label' => [
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'type' => [
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::TEXT,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::TEXT,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::INTEGER,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::INTEGER,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DECIMAL,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DECIMAL,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DATE,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DATE,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DATETIME,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DATETIME,
                    ],
                ],
                'eval' => 'required',
            ],
        ],
        'decimal_places' => [
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.decimal_places',
            'displayCond' => 'FIELD:type:=:' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::DECIMAL,
            'config' => [
                'type' => 'input',
                'size' => 3,
                'eval' => 'int,trim',
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
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.field_size',
            'displayCond' => [
                'OR' => [
                    'REC:NEW:true',
                    'FIELD:type:=:' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::TEXT,
                ],
            ],
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'range' => [
                    'lower' => 0,
                ],
                'eval' => 'int',
                'default' => 0,
            ],
        ],
        'alignment' => [
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        '',
                    ],
                    [
                        Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment.left',
                        'left',
                    ],
                    [
                        Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment.center',
                        'center',
                    ],
                    [
                        Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.alignment.right',
                        'right',
                    ],
                ],
            ],
        ],
        'sorting_priority' => [
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_priority',
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
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_order',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        '',
                    ],
                    [
                        Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_order.asc',
                        'asc',
                    ],
                    [
                        Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.sorting_order.desc',
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
            'label' => Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':palette.rendering_ce',
            'showitem' => 'alignment, sorting_priority, sorting_order',
        ],
    ],
];
