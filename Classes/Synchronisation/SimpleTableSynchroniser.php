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
use JobRouter\AddOn\Typo3Data\Domain\Repository\ColumnRepository;
use JobRouter\AddOn\Typo3Data\Event\ModifyDatasetOnSynchronisationEvent;
use JobRouter\AddOn\Typo3Data\Exception\SynchronisationException;
use JobRouter\AddOn\Typo3Data\Extension;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
final class SimpleTableSynchroniser
{
    private const DATASET_TABLE_NAME = 'tx_jobrouterdata_domain_model_dataset';

    public function __construct(
        private readonly FrontendInterface $cache,
        private readonly ColumnRepository $columnRepository,
        private readonly ConnectionPool $connectionPool,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly SynchronisationService $synchronisationService,
    ) {}

    public function synchroniseTable(Table $table, bool $force): bool
    {
        try {
            $datasets = $this->synchronisationService->retrieveDatasetsFromJobDataTable($table);
            $this->storeDatasets($table, $datasets, $force);
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

    private function storeDatasets(Table $table, array $datasets, bool $force): void
    {
        $datasetsHash = $this->synchronisationService->hashDatasets($datasets);

        $this->logger->debug('Data sets hash: ' . $datasetsHash . ' vs existing: ' . $table->datasetsSyncHash);

        if (! $force && $datasetsHash === $table->datasetsSyncHash) {
            $this->synchronisationService->updateSynchronisationStatus($table);
            $this->logger->info('Datasets have not changed', [
                'table_uid' => $table->uid,
            ]);
            return;
        }

        $datasetConnection = $this->connectionPool->getConnectionForTable(self::DATASET_TABLE_NAME);
        $datasetConnection->setAutoCommit(false);
        $datasetConnection->beginTransaction();

        try {
            $this->deleteAllOldDatasets($table);
            foreach ($datasets as $dataset) {
                $this->insertNewDataset($table, $dataset);
            }
            $this->synchronisationService->updateSynchronisationStatus($table, $datasetsHash);
        } catch (\Exception $e) {
            $datasetConnection->rollBack();
            $datasetConnection->setAutoCommit(true);

            $this->synchronisationService->updateSynchronisationStatus($table, $table->datasetsSyncHash, $e->getMessage());

            $this->logger->emergency(
                'Error while synchronising, rollback',
                [
                    'table handle' => $table->handle,
                    'message' => $e->getMessage(),
                ],
            );

            throw new SynchronisationException($e->getMessage(), 1567014608, $e);
        }

        $datasetConnection->commit();
        $datasetConnection->setAutoCommit(true);

        $this->clearCacheForTable($table->uid);
    }

    private function deleteAllOldDatasets(Table $table): void
    {
        $datasetConnection = $this->connectionPool->getConnectionForTable(self::DATASET_TABLE_NAME);
        $datasetConnection->delete(
            self::DATASET_TABLE_NAME,
            [
                'table_uid' => $table->uid,
            ],
            [
                'table_uid' => \PDO::PARAM_INT,
            ],
        );

        $this->logger->debug('Deleted existing data sets in transaction', [
            'table_uid' => $table->uid,
        ]);
    }

    /**
     * @param array<string, string|int|float|bool|null> $dataset
     */
    private function insertNewDataset(Table $table, array $dataset): void
    {
        $event = new ModifyDatasetOnSynchronisationEvent($table, $dataset);
        /** @var ModifyDatasetOnSynchronisationEvent $event */
        $event = $this->eventDispatcher->dispatch($event);
        if ($event->isRejected()) {
            return;
        }

        $columns = $this->columnRepository->findByTableUid($table->uid);
        $dataset = $event->getDataset();
        $datasetToStore = [];
        foreach ($columns as $column) {
            if (\array_key_exists($column->name, $dataset)) {
                $datasetToStore[$column->name] = $dataset[$column->name];
            }
        }

        $data = [
            'table_uid' => $table->uid,
            'jrid' => $dataset['jrid'],
            'dataset' => \json_encode($datasetToStore, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR),
        ];

        $datasetConnection = $this->connectionPool->getConnectionForTable(self::DATASET_TABLE_NAME);
        $datasetConnection->insert(
            self::DATASET_TABLE_NAME,
            $data,
            [
                'table_uid' => \PDO::PARAM_INT,
                'jrid' => \PDO::PARAM_INT,
                'dataset' => \PDO::PARAM_STR,
            ],
        );

        $this->logger->debug('Inserted data set in transaction', $data);
    }

    private function clearCacheForTable(int $tableUid): void
    {
        $tag = \sprintf(Extension::CACHE_TAG_TABLE_TEMPLATE, $tableUid);
        $this->cache->flushByTag($tag);
    }
}
