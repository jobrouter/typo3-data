<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Hooks;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * @internal
 */
final class TableUpdateHook
{
    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
    }

    public function processCmdmap_postProcess($command, $table, $recordId, $commandValue, DataHandler $dataHandler): void
    {
        if ($table === 'tx_jobrouterdata_domain_model_table' && $command === 'delete') {
            /** @var Connection $connection */
            $connection = $this->connectionPool->getConnectionForTable('tx_jobrouterdata_domain_model_dataset');

            $connection->delete(
                'tx_jobrouterdata_domain_model_dataset',
                [
                    'table_uid' => $recordId,
                ],
                [
                    'table_uid' => Connection::PARAM_INT,
                ]
            );
        }
    }
}
