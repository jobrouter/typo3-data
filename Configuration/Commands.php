<?php
return [
    'jobrouter:data:sync' => [
        'class' => \Brotkrueml\JobRouterData\Command\SyncCommand::class,
        'schedulable' => true,
    ],
    'jobrouter:data:transmit' => [
        'class' => \Brotkrueml\JobRouterData\Command\TransmitCommand::class,
        'schedulable' => true,
    ],
];
