<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Demand;

use JobRouter\AddOn\Typo3Connector\Domain\Repository\ConnectionRepository;
use JobRouter\AddOn\Typo3Connector\Exception\ConnectionNotFoundException;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Domain\Repository\ColumnRepository;

/**
 * @internal
 */
final class TableDemandFactory
{
    public function __construct(
        private readonly ColumnRepository $columnRepository,
        private readonly ConnectionRepository $connectionRepository,
    ) {}

    public function create(Table $table, bool $withDisabled = false): TableDemand
    {
        try {
            $connection = $this->connectionRepository->findByUid($table->connectionUid, $withDisabled);
        } catch (ConnectionNotFoundException) {
            $connection = null;
        }
        $columns = $this->columnRepository->findByTableUid($table->uid);

        return new TableDemand(
            $table->uid,
            $connection,
            $table->type,
            $table->handle,
            $table->name,
            $table->tableGuid,
            $table->customTable,
            $table->datasetsSyncHash,
            $table->lastSyncDate,
            $table->lastSyncError,
            $columns,
            $table->disabled,
        );
    }

    /**
     * @param Table[] $tables
     * @return TableDemand[]
     */
    public function createMultiple(array $tables, bool $withDisabled = false): array
    {
        $demands = [];
        foreach ($tables as $table) {
            $demands[] = $this->create($table, $withDisabled);
        }

        return $demands;
    }
}
