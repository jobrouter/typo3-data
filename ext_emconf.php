<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Connect JobRouter® JobData tables with TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_company' => 'JobRouter GmbH',
    'state' => 'stable',
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'jobrouter_base' => '5.0.0-5.99.99',
            'jobrouter_connector' => '5.0.0-5.99.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'dashboard' => '',
            'form' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => ['JobRouter\\AddOn\\Typo3Data\\' => 'Classes']
    ],
];
