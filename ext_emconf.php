<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Connect JobRouter® JobData tables with TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'beta',
    'version' => '0.12.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'form' => '10.4.0-10.4.99',
            'jobrouter_base' => '0.1.0-0.1.99',
            'jobrouter_connector' => '0.11.0-0.11.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'dashboard' => '',
            'logs' => ''
        ],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterData\\' => 'Classes']
    ],
];
