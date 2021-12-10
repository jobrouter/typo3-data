<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Synchronisation;

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

/**
 * @internal
 */
final class CustomTableSynchroniser extends AbstractSynchroniser
{
    public function synchroniseTable(Table $table): bool
    {
        try {
            $datasets = $this->retrieveDatasetsFromJobRouter($table);
            $columns = $this->getCustomTableColumns($table);
            $this->storeDatasets($table, $columns, $datasets);
            $this->updateSynchronisationStatus($table);
        } catch (\Exception $e) {
            $message = \sprintf(
                'Table link with uid "%d" cannot be synchronised: %s',
                $table->getUid(),
                $e->getMessage()
            );

            $this->logger->error($message);
            $this->updateSynchronisationStatus($table, null, $message);

            return false;
        }

        return true;
    }

    /**
     * @return int[]|string[]
     */
    private function getCustomTableColumns(Table $table): array
    {
        $customTable = $table->getCustomTable();

        $connection = $this->connectionPool->getConnectionForTable($customTable);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();

        return \array_keys($schemaManager->listTableColumns($customTable));
    }

    private function storeDatasets(Table $table, array $customTableColumns, array $datasets): void
    {
        $datasetsHash = $this->hashDatasets($datasets);

        if ($datasetsHash === $table->getDatasetsSyncHash()) {
            $this->updateSynchronisationStatus($table);
            $this->logger->info('Datasets have not changed', [
                'table_uid' => $table->getUid(),
            ]);
            return;
        }

        $customTable = $table->getCustomTable();

        $connection = $this->connectionPool->getConnectionForTable($customTable);
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->truncate($customTable);
            $this->logger->debug('Truncated table in transaction', [
                'custom table' => $customTable,
            ]);

            foreach ($datasets as $dataset) {
                $event = new ModifyDatasetOnSynchronisationEvent(clone $table, $dataset);
                /** @var ModifyDatasetOnSynchronisationEvent $event */
                $event = $this->eventDispatcher->dispatch($event);
                if ($event->isRejected()) {
                    continue;
                }

                $dataset = $event->getDataset();
                $data = [];
                foreach ($dataset as $column => $content) {
                    $column = \strtolower($column);
                    if (! \in_array($column, $customTableColumns, true)) {
                        continue;
                    }

                    $data[$column] = $content;
                }

                $connection->insert($customTable, $data);

                $this->logger->debug('Inserted table record in transaction', $data);
            }

            $this->updateSynchronisationStatus($table, $datasetsHash);
        } catch (\Exception $e) {
            $connection->rollBack();
            $connection->setAutoCommit(true);

            $this->updateSynchronisationStatus($table, $table->getDatasetsSyncHash(), $e->getMessage());

            $this->logger->emergency(
                'Error while synchronising, rollback',
                [
                    'table uid' => $table->getUid(),
                    'custom table' => $customTable,
                    'message' => $e->getMessage(),
                ]
            );

            throw new SynchronisationException($e->getMessage(), 1567799062, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);
    }
}
