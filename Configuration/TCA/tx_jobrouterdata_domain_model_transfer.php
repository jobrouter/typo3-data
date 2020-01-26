<?php
return [
    'ctrl' => [
        'title' => 'Transfer',
        'label' => 'identifier',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'table_uid, identifier, transmit_success, transmit_date, transmit_message',
    ],
    'columns' => [
        'table_uid' => [
            'label' => 'Table',
            'config' => [
                'type' => 'input',
            ],
        ],
        'identifier' => [
            'label' => 'Identifier',
            'config' => [
                'type' => 'input',
            ],
        ],
        'data' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_success' => [
            'label' => 'Transmit success',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_date' => [
            'label' => 'Transmit date',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_message' => [
            'label' => 'Transmit message',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'table_uid, identifier, data, transmit_success, transmit_date, transmit_message'],
    ],
];
