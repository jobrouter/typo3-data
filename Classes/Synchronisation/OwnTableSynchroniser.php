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
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class OwnTableSynchroniser extends AbstractSynchroniser
{
    public function synchroniseTable(Table $table): void
    {
        $datasets = $this->retrieveDatasetsFromJobRouter($table);
        $columns = $this->getOwnTableColumns($table);

        $this->storeDatasets($table, $columns, $datasets);
    }

    private function getOwnTableColumns(Table $table): array
    {
        $ownTable = $table->getOwnTable();

        $connection = $this->connectionPool->getConnectionForTable($ownTable);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();

        return \array_keys($schemaManager->listTableColumns($ownTable));
    }

    private function storeDatasets(Table $table, array $ownTableColumns, array $datasets): void
    {
        $datasetsHash = $this->hashDatasets($datasets);

        if ($datasetsHash === $table->getDatasetsSyncHash()) {
            $this->logger->info('Datasets have not changed', ['table_uid' => $table->getUid()]);
            return;
        }

        $ownTable = $table->getOwnTable();

        $connection = $this->connectionPool->getConnectionForTable($ownTable);
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->truncate($ownTable);
            $this->logger->debug('Truncated table in transaction', ['own table' => $ownTable]);

            foreach ($datasets as $dataset) {
                $data = [];

                foreach ($dataset as $column => $content) {
                    $column = \strtolower($column);
                    if (!\in_array($column, $ownTableColumns)) {
                        continue;
                    }

                    $data[$column] = $content;
                }

                $connection->insert($ownTable, $data);

                $connection->update(
                    'tx_jobrouterdata_domain_model_table',
                    ['datasets_sync_hash' => $datasetsHash],
                    ['uid' => $table->getUid()],
                    ['datasets_sync_hash' => \PDO::PARAM_STR]
                );

                $this->logger->debug('Inserted table record in transaction', $data);
            }
        } catch (\Exception $e) {
            $connection->rollBack();

            $this->logger->emergency(
                'Error while synchronising, rollback',
                ['table uid' => $table->getUid(), 'own table' => $ownTable, 'message' => $e->getMessage()]
            );

            throw new SynchronisationException($e->getMessage(), 1567799062, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);
    }
}
