<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Synchronisation;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Event\ModifyDatasetOnSynchronisationEvent;
use JobRouter\AddOn\Typo3Data\Exception\SynchronisationException;
use JobRouter\AddOn\Typo3Data\Table\TableProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
final class CustomTableSynchroniser
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly SynchronisationService $synchronisationService,
        private readonly TableProvider $tableProvider,
    ) {}

    public function synchroniseTable(Table $table, bool $force): bool
    {
        try {
            $datasets = $this->synchronisationService->retrieveDatasetsFromJobDataTable($table);
            $columns = $this->tableProvider->getColumnsForCustomTable($table->customTable);
            $this->storeDatasets($table, $columns, $datasets, $force);
            $this->synchronisationService->updateSynchronisationStatus($table);
        } catch (\Exception $e) {
            $message = \sprintf(
                'Table link with uid "%d" cannot be synchronised: %s',
                $table->uid,
                $e->getMessage(),
            );

            $this->logger->error($message);
            $this->synchronisationService->updateSynchronisationStatus($table, '', $message);

            return false;
        }

        return true;
    }

    private function storeDatasets(Table $table, array $customTableColumns, array $datasets, bool $force): void
    {
        $datasetsHash = $this->synchronisationService->hashDatasets($datasets);

        if (! $force && $datasetsHash === $table->datasetsSyncHash) {
            $this->synchronisationService->updateSynchronisationStatus($table);
            $this->logger->info('Datasets have not changed', [
                'table_uid' => $table->uid,
            ]);
            return;
        }

        $connection = $this->connectionPool->getConnectionForTable($table->customTable);
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->truncate($table->customTable);
            $this->logger->debug('Truncated table in transaction', [
                'custom table' => $table->customTable,
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

                $connection->insert($table->customTable, $data);

                $this->logger->debug('Inserted table record in transaction', $data);
            }

            $this->synchronisationService->updateSynchronisationStatus($table, $datasetsHash);
        } catch (\Exception $e) {
            $connection->rollBack();
            $connection->setAutoCommit(true);

            $this->synchronisationService->updateSynchronisationStatus($table, $table->datasetsSyncHash, $e->getMessage());

            $this->logger->emergency(
                'Error while synchronising, rollback',
                [
                    'table handle' => $table->handle,
                    'custom table' => $table->customTable,
                    'message' => $e->getMessage(),
                ],
            );

            throw new SynchronisationException($e->getMessage(), 1567799062, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);
    }
}
