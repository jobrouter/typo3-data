<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Synchronise JobData tables into TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'alpha',
    'version' => '0.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'jobrouter_connector' => '0.4.0-0.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterData\\' => 'Classes']
    ],
];
