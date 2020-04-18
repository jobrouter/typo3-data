<?php

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'jobrouter:data:sync' => [
        'class' => \Brotkrueml\JobRouterData\Command\SyncCommand::class,
        'schedulable' => true,
    ],
    'jobrouter:data:transmit' => [
        'class' => \Brotkrueml\JobRouterData\Command\TransmitCommand::class,
        'schedulable' => true,
    ],
    'jobrouter:data:cleanuptransfers' => [
        'class' => \Brotkrueml\JobRouterData\Command\CleanUpTransfersCommand::class,
        'schedulable' => true,
    ],
];
