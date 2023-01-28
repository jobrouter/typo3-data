<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Demand;

use Brotkrueml\JobRouterConnector\Domain\Repository\ConnectionRepository;
use Brotkrueml\JobRouterConnector\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterData\Domain\Entity\Table;
use Brotkrueml\JobRouterData\Domain\Repository\ColumnRepository;

/**
 * @internal
 */
final class TableDemandFactory
{
    public function __construct(
        private readonly ColumnRepository $columnRepository,
        private readonly ConnectionRepository $connectionRepository,
    ) {
    }

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
