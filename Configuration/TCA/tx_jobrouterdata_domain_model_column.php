<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column',
        'label' => 'label',
        'label_alt' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'rootLevel' => 1,
        'searchFields' => 'name,label',
        'iconfile' => 'EXT:' . \Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_column.svg',
        'hideTable' => true,
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ]
        ],

        'name' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 20,
                'eval' => 'required,trim',
            ],
        ],
        'label' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', ''],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type.text',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::TEXT
                    ],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type.integer',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::INTEGER
                    ],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type.decimal',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DECIMAL
                    ],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type.date',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DATE
                    ],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.type.datetime',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DATETIME
                    ],
                ],
                'eval' => 'required',
            ],
        ],
        'decimal_places' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_column.decimal_places',
            'displayCond' => 'FIELD:type:=:' . \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DECIMAL,
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
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameLabel,
                --palette--;;type,
            '
        ],
    ],
    'palettes' => [
        'nameLabel' => ['showitem' => 'name, label'],
        'type' => ['showitem' => 'type, decimal_places'],
    ],
];
