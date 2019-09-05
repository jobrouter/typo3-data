<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_dataset',
        'label' => 'name',
        'rootLevel' => 1,
        'searchFields' => 'name,label',
        'iconfile' => 'EXT:jobrouter_data/Resources/Public/Icons/tx_jobrouterdata_domain_model_dataset.svg',
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'jrid, dataset',
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],

        'jrid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_dataset.jrid',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 20,
                'eval' => 'required,int',
                'readOnly' => true,
            ],
        ],
        'dataset' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_data/Resources/Private/Language/Database.xlf:tx_jobrouterdata_domain_model_dataset.dataset',
            'config' => [
                'type' => 'text',
                'eval' => 'required',
                'readOnly' => true,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                jrid, dataset,
            '
        ],
    ],
];
