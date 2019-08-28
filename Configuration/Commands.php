<?php
return [
    'jobrouter:data:sync' => [
        'class' => \Brotkrueml\JobRouterData\Command\SyncCommand::class,
        'schedulable' => true,
    ],
];