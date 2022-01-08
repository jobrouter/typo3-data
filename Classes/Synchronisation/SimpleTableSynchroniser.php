<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Synchronisation;

use Brotkrueml\JobRouterData\Cache\Cache;
use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Database\Connection;

/**
 * @internal
 */
final class SimpleTableSynchroniser implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const DATASET_TABLE_NAME = 'tx_jobrouterdata_domain_model_dataset';

    /**
     * @var Connection
     */
    private $datasetConnection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var SynchronisationService
     */
    private $synchronisationService;

    public function __construct(Connection $datasetConnection, EventDispatcherInterface $eventDispatcher, SynchronisationService $synchronisationService)
    {
        $this->datasetConnection = $datasetConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->synchronisationService = $synchronisationService;
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

        $this->datasetConnection->setAutoCommit(false);
        $this->datasetConnection->beginTransaction();

        try {
            $this->deleteAllOldDatasets($table);
            foreach ($datasets as $dataset) {
                $this->insertNewDataset($table, $dataset);
            }
            $this->synchronisationService->updateSynchronisationStatus($table, $datasetsHash);
        } catch (\Exception $e) {
            $this->datasetConnection->rollBack();
            $this->datasetConnection->setAutoCommit(true);

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

        $this->datasetConnection->commit();
        $this->datasetConnection->setAutoCommit(true);

        Cache::clearCacheByTable($table->getUid());
    }

    private function deleteAllOldDatasets(Table $table): void
    {
        $this->datasetConnection->delete(
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

        $this->datasetConnection->insert(
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
}
