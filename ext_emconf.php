<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Connect JobRouter® JobData tables with TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '11.5.4-12.4.99',
            'jobrouter_base' => '2.0.0-2.99.99',
            'jobrouter_connector' => '2.0.0-2.99.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'dashboard' => '',
            'form' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterData\\' => 'Classes']
    ],
];
