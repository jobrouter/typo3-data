<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'name,table_guid',
        'iconfile' => 'EXT:jobrouter_data/Resources/Public/Icons/tx_jobrouterdata_domain_model_table.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'disabled, name, connection, table_guid, columns',
    ],
    'columns' => [
        'disabled' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ]
        ],

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
                'eval' => 'alphanum_x,required,trim,unique'
            ],
        ],
        'connection' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.connection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['' => ''],
                ],
                'foreign_table' => 'tx_jobrouterconnector_domain_model_connection',
                'foreign_table_where' => ' ORDER BY tx_jobrouterconnector_domain_model_connection.name',
                'eval' => 'required'
            ],
        ],
        'table_guid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.table_guid',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 36,
                'eval' => 'alphanum_x,required,trim,upper'
            ],
        ],
        'columns' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.columns',
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
                    'showPossibleLocalizationRecords' => false,
                    'showSynchronizationLink' => false,
                    'enabledControls' => [
                        'info' => false,
                    ]
                ],
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => '
            name, connection, table_guid, columns,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '],
    ],
];
