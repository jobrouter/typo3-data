<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'rootLevel' => 1,
        'searchFields' => 'name,label',
        'iconfile' => 'EXT:jobrouter_data/Resources/Public/Icons/tx_jobrouterdata_domain_model_column.svg',
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'name, type, label',
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
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 20,
                'eval' => 'required,trim',
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', ''],
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.type.text',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::TEXT
                    ],
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.type.integer',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::INTEGER
                    ],
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.type.decimal',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DECIMAL
                    ],
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.type.date',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DATE
                    ],
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.type.datetime',
                        \Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration::DATETIME
                    ],
                ],
                'eval' => 'required',
            ],
        ],
        'label' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_column.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                name, type, label,
            '
        ],
    ],
];
