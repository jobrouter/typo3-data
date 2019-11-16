<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Synchronisation;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;

class SimpleTableSynchroniser extends AbstractSynchroniser
{
    private const DATASET_TABLE_NAME = 'tx_jobrouterdata_domain_model_dataset';

    public function synchroniseTable(Table $table): void
    {
        $datasets = $this->retrieveDatasetsFromJobRouter($table);
        $this->storeDatasets($table, $datasets);
    }

    private function storeDatasets(Table $table, array $datasets): void
    {
        $connection = $this->connectionPool->getConnectionForTable(static::DATASET_TABLE_NAME);
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->delete(
                static::DATASET_TABLE_NAME,
                ['table_uid' => $table->getUid()],
                ['table_uid' => \PDO::PARAM_INT]
            );

            $this->logger->debug('Deleted table records in transaction', ['table_uid' => $table->getUid()]);

            foreach ($datasets as $dataset) {
                $jrid = $dataset['jrid'];
                unset($dataset['jrid']);

                $data = [
                    'pid' => 0,
                    'table_uid' => $table->getUid(),
                    'jrid' => $jrid,
                    'dataset' => \json_encode($dataset, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ];

                $connection->insert(
                    static::DATASET_TABLE_NAME,
                    $data,
                    [
                        'pid' => \PDO::PARAM_INT,
                        'table_uid' => \PDO::PARAM_INT,
                        'jrid' => \PDO::PARAM_INT,
                        'dataset' => \PDO::PARAM_STR,
                    ]
                );

                $this->logger->debug('Inserted table record in transaction', $data);
            }
        } catch (\Exception $e) {
            $connection->rollBack();

            $this->logger->emergency(
                'Error while synchronising, rollback',
                ['table uid' => $table->getUid(), 'message' => $e->getMessage()]
            );

            throw new SynchronisationException($e->getMessage(), 1567014608, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);
    }
}
