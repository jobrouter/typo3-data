<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'JobData Transfer',
        'label' => 'identifier',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'hideTable' => true,
        'iconfile' => 'EXT:' . \Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_transfer.svg'
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
