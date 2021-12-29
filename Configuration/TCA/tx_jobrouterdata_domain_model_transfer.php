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
        'label' => 'uid',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'hideTable' => true,
        'iconfile' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterdata_domain_model_transfer.svg',
    ],
    'columns' => [
        'crdate' => [
            'label' => 'Crdate',
            'config' => [
                'type' => 'input',
            ],
        ],
        'table_uid' => [
            'label' => 'Table',
            'config' => [
                'type' => 'input',
            ],
        ],
        'correlation_id' => [
            'label' => 'Correlation id',
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
        '1' => [
            'showitem' => 'table_uid, identifier, data, transmit_success, transmit_date, transmit_message',
        ],
    ],
];
