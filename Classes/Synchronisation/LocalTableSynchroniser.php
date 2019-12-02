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

class LocalTableSynchroniser extends AbstractSynchroniser
{
    public function synchroniseTable(Table $table): void
    {
        $datasets = $this->retrieveDatasetsFromJobRouter($table);
        $columns = $this->getLocalTableColumns($table);

        $this->storeDatasets($table, $columns, $datasets);
    }

    private function getLocalTableColumns(Table $table): array
    {
        $localTable = $table->getLocalTable();

        $connection = $this->connectionPool->getConnectionForTable($localTable);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();

        return \array_keys($schemaManager->listTableColumns($localTable));
    }

    private function storeDatasets(Table $table, array $localTableColumns, array $datasets): void
    {
        $datasetsHash = $this->hashDatasets($datasets);

        if ($datasetsHash === $table->getDatasetsSyncHash()) {
            $this->logger->info('Datasets have not changed', ['table_uid' => $table->getUid()]);
            return;
        }

        $localTable = $table->getLocalTable();

        $connection = $this->connectionPool->getConnectionForTable($localTable);
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->truncate($localTable);
            $this->logger->debug('Truncated table in transaction', ['local table' => $localTable]);

            foreach ($datasets as $dataset) {
                $data = [];

                foreach ($dataset as $column => $content) {
                    $column = \strtolower($column);
                    if (!\in_array($column, $localTableColumns)) {
                        continue;
                    }

                    $data[$column] = $content;
                }

                $connection->insert($localTable, $data);

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
                ['table uid' => $table->getUid(), 'local table' => $localTable, 'message' => $e->getMessage()]
            );

            throw new SynchronisationException($e->getMessage(), 1567799062, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);
    }
}
