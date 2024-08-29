<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Data',
    'description' => 'Connect JobRouter® JobData tables with TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_company' => 'JobRouter AG',
    'state' => 'stable',
    'version' => '4.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '12.4.0-12.4.99',
            'jobrouter_base' => '4.0.0-4.99.99',
            'jobrouter_connector' => '4.0.0-4.99.99',
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
