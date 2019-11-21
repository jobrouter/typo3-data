<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'type' => 'type',
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

        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.type.simple_synchronisation',
                        \Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_SIMPLE
                    ],
                    [
                        'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.type.synchronise_in_local_table',
                        \Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_LOCAL_TABLE
                    ],
                ],
            ],
        ],
        'connection' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.connection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_jobrouterconnector_domain_model_connection',
                'foreign_table_where' => ' ORDER BY tx_jobrouterconnector_domain_model_connection.name',
                'eval' => 'int,required',
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'required,trim'
            ],
        ],
        'table_guid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.table_guid',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 36,
                'eval' => 'alphanum_x,required,trim,upper',
            ],
        ],
        'local_table' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.local_table',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['' => ''],
                ],
                'itemsProcFunc' => \Brotkrueml\JobRouterData\Service\LocalSynchronisationTables::class . '->getLocalTables',
                'eval' => 'required',
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
                    'enabledControls' => [
                        'info' => false,
                    ],
                ],
            ],
        ],
        'datasets' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_table.datasets',
            'config' => [
                'type' => 'inline',
                'allowed' => 'tx_jobrouterdata_domain_model_dataset',
                'foreign_table' => 'tx_jobrouterdata_domain_model_dataset',
                'foreign_sortby' => 'uid',
                'foreign_field' => 'table_uid',
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
    ],
    'types' => [
        '1' => ['showitem' => '
            type, connection, name, table_guid, columns,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '],
        '2' => ['showitem' => '
            type, connection, name, table_guid, local_table,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '],
    ],
];
