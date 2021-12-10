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
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;

/**
 * @internal
 */
final class SimpleTableSynchroniser extends AbstractSynchroniser
{
    private const DATASET_TABLE_NAME = 'tx_jobrouterdata_domain_model_dataset';

    public function synchroniseTable(Table $table): bool
    {
        try {
            $datasets = $this->retrieveDatasetsFromJobRouter($table);
            $this->storeDatasets($table, $datasets);
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

    private function storeDatasets(Table $table, array $datasets): void
    {
        $datasetsHash = $this->hashDatasets($datasets);

        $this->logger->debug('Data sets hash: ' . $datasetsHash . ' vs existing: ' . $table->getDatasetsSyncHash());

        if ($datasetsHash === $table->getDatasetsSyncHash()) {
            $this->updateSynchronisationStatus($table);
            $this->logger->info('Data sets have not changed', [
                'table_uid' => $table->getUid(),
            ]);
            return;
        }

        $connection = $this->connectionPool->getConnectionForTable(self::DATASET_TABLE_NAME);
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->delete(
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

            foreach ($datasets as $dataset) {
                $event = new ModifyDatasetOnSynchronisationEvent(clone $table, $dataset);
                /** @var ModifyDatasetOnSynchronisationEvent $event */
                $event = $this->eventDispatcher->dispatch($event);
                if ($event->isRejected()) {
                    continue;
                }

                $dataset = $event->getDataset();
                $jrid = $dataset['jrid'];
                unset($dataset['jrid']);

                $data = [
                    'pid' => 0,
                    'table_uid' => $table->getUid(),
                    'jrid' => $jrid,
                    'dataset' => \json_encode($dataset, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ];

                $connection->insert(
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

            $this->updateSynchronisationStatus($table, $datasetsHash);
        } catch (\Exception $e) {
            $connection->rollBack();
            $connection->setAutoCommit(true);

            $this->updateSynchronisationStatus($table, $table->getDatasetsSyncHash(), $e->getMessage());

            $this->logger->emergency(
                'Error while synchronising, rollback',
                [
                    'table uid' => $table->getUid(),
                    'message' => $e->getMessage(),
                ]
            );

            throw new SynchronisationException($e->getMessage(), 1567014608, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);

        Cache::clearCacheByTable($table->getUid());
    }
}
