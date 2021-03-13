<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Connect JobRouter® JobData tables with TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'beta',
    'version' => '0.13.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.11-10.4.99',
            'jobrouter_base' => '0.1.1-0.1.99',
            'jobrouter_connector' => '0.12.0-0.12.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'dashboard' => '',
            'form' => '',
            'logs' => ''
        ],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterData\\' => 'Classes']
    ],
];
