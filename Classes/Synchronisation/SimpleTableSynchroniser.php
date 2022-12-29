<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Synchronisation;

use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use Brotkrueml\JobRouterData\Extension;
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
        private readonly ConnectionPool $connectionPool,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly SynchronisationService $synchronisationService
    ) {
    }

    public function synchroniseTable(Table $table, bool $force): bool
    {
        try {
            $datasets = $this->synchronisationService->retrieveDatasetsFromJobDataTable($table);
            $this->storeDatasets($table, $datasets, $force);
            $this->synchronisationService->updateSynchronisationStatus($table);
        } catch (\Exception $e) {
            $message = \sprintf(
                'Table link with uid "%d" cannot be synchronised: %s',
                $table->getUid(),
                $e->getMessage()
            );

            $this->logger->error($message);
            $this->synchronisationService->updateSynchronisationStatus($table, null, $message);

            return false;
        }

        return true;
    }

    private function storeDatasets(Table $table, array $datasets, bool $force): void
    {
        $datasetsHash = $this->synchronisationService->hashDatasets($datasets);

        $this->logger->debug('Data sets hash: ' . $datasetsHash . ' vs existing: ' . $table->getDatasetsSyncHash());

        if (! $force && $datasetsHash === $table->getDatasetsSyncHash()) {
            $this->synchronisationService->updateSynchronisationStatus($table);
            $this->logger->info('Datasets have not changed', [
                'table_uid' => $table->getUid(),
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

            $this->synchronisationService->updateSynchronisationStatus($table, $table->getDatasetsSyncHash(), $e->getMessage());

            $this->logger->emergency(
                'Error while synchronising, rollback',
                [
                    'table handle' => $table->getHandle(),
                    'message' => $e->getMessage(),
                ]
            );

            throw new SynchronisationException($e->getMessage(), 1567014608, $e);
        }

        $datasetConnection->commit();
        $datasetConnection->setAutoCommit(true);

        $this->clearCacheForTable($table->getUid());
    }

    private function deleteAllOldDatasets(Table $table): void
    {
        $datasetConnection = $this->connectionPool->getConnectionForTable(self::DATASET_TABLE_NAME);
        $datasetConnection->delete(
            self::DATASET_TABLE_NAME,
            [
                'table_uid' => $table->getUid(),
            ],
            [
                'table_uid' => \PDO::PARAM_INT,
            ]
        );

        $this->logger->debug('Deleted existing data sets in transaction', [
            'table_uid' => $table->getUid(),
        ]);
    }

    /**
     * @param array<string, string|int|float|bool|null> $dataset
     */
    private function insertNewDataset(Table $table, array $dataset): void
    {
        $event = new ModifyDatasetOnSynchronisationEvent(clone $table, $dataset);
        /** @var ModifyDatasetOnSynchronisationEvent $event */
        $event = $this->eventDispatcher->dispatch($event);
        if ($event->isRejected()) {
            return;
        }

        $dataset = $event->getDataset();
        $datasetToStore = [];
        foreach ($table->getColumns() as $column) {
            /** @var Column $column */
            if (\array_key_exists($column->getName(), $dataset)) {
                $datasetToStore[$column->getName()] = $dataset[$column->getName()];
            }
        }

        $data = [
            'pid' => 0,
            'table_uid' => $table->getUid(),
            'jrid' => $dataset['jrid'],
            'dataset' => \json_encode($datasetToStore, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR),
        ];

        $datasetConnection = $this->connectionPool->getConnectionForTable(self::DATASET_TABLE_NAME);
        $datasetConnection->insert(
            self::DATASET_TABLE_NAME,
            $data,
            [
                'pid' => \PDO::PARAM_INT,
                'table_uid' => \PDO::PARAM_INT,
                'jrid' => \PDO::PARAM_INT,
                'dataset' => \PDO::PARAM_STR,
            ]
        );

        $this->logger->debug('Inserted data set in transaction', $data);
    }

    private function clearCacheForTable(int $tableUid): void
    {
        $tag = \sprintf(Extension::CACHE_TAG_TABLE_TEMPLATE, $tableUid);
        $this->cache->flushByTag($tag);
    }
}
