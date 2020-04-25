<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table',
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
        'iconfile' => 'EXT:' . \Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_table.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'disabled, name, connection, table_guid, columns, last_sync_date, last_sync_error',
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
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.simple_synchronisation',
                        \Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_SIMPLE
                    ],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.synchronisation_in_own_table',
                        \Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_OWN_TABLE
                    ],
                    [
                        \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.type.other_usage',
                        \Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_OTHER_USAGE
                    ],
                ],
            ],
        ],
        'connection' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.connection',
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
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'required,trim'
            ],
        ],
        'table_guid' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.table_guid',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 36,
                'eval' => 'alphanum_x,required,trim,upper',
            ],
        ],
        'own_table' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.own_table',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['' => ''],
                ],
                'itemsProcFunc' => \Brotkrueml\JobRouterData\Service\OwnTables::class . '->getTables',
                'eval' => 'required',
            ],
        ],
        'columns' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.columns',
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
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.datasets',
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
        'datasets_sync_hash' => [
            // Not to be shown, relevant for model
            'label' => 'Data sets sync hash',
            'config' => [
                'type' => 'input',
            ]
        ],
        'last_sync_date' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.last_sync_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'last_sync_error' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterdata_domain_model_table.last_sync_error',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'readOnly' => true,
            ],
        ],
    ],
    'types' => [
        (string)\Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_SIMPLE => [
            'showitem' => '
            type, connection, name, table_guid, columns,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;' . \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tab.status,
            --palette--;;synchronisationStatus,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '
        ],
        (string)\Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_OWN_TABLE => [
            'showitem' => '
            type, connection, name, table_guid, own_table,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;' . \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':tab.status,
            --palette--;;synchronisationStatus,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '
        ],
        (string)\Brotkrueml\JobRouterData\Domain\Model\Table::TYPE_OTHER_USAGE => [
            'showitem' => '
            type, connection, name, table_guid,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '
        ],
    ],
    'palettes' => [
        'synchronisationStatus' => [
            'label' => \Brotkrueml\JobRouterData\Extension::LANGUAGE_PATH_DATABASE . ':palette.last_synchronisation',
            'showitem' => 'last_sync_date, --linebreak--, last_sync_error'
        ],
    ],
];
