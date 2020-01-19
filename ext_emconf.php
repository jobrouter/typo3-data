<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Synchronise JobData tables into TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'beta',
    'version' => '0.6.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'jobrouter_connector' => '0.6.0-0.6.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterData\\' => 'Classes']
    ],
];
