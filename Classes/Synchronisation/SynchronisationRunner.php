<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Synchronisation;

use JobRouter\AddOn\Typo3Data\Domain\Dto\CountResult;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Enumerations\TableType;
use JobRouter\AddOn\Typo3Data\Exception\SynchronisationException;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
class SynchronisationRunner
{
    private int $totalTables = 0;
    private int $erroneousTables = 0;

    public function __construct(
        private readonly CustomTableSynchroniser $customTableSynchroniser,
        private readonly LoggerInterface $logger,
        private readonly SimpleTableSynchroniser $simpleTableSynchroniser,
        private readonly TableRepository $tableRepository,
    ) {}

    public function run(string $tableHandle, bool $force): CountResult
    {
        if ($tableHandle === '') {
            $tables = $this->tableRepository->findAll();
            foreach ($tables as $table) {
                $this->synchroniseTable($table, $force);
            }
        } else {
            $this->synchroniseTable($this->getTable($tableHandle), $force);
        }

        return new CountResult($this->totalTables, $this->erroneousTables);
    }

    private function synchroniseTable(Table $table, bool $force): void
    {
        if ($table->type === TableType::Simple) {
            $this->totalTables++;
            if (! $this->simpleTableSynchroniser->synchroniseTable($table, $force)) {
                $this->erroneousTables++;
            }
            return;
        }

        if ($table->type === TableType::CustomTable) {
            $this->totalTables++;
            if (! $this->customTableSynchroniser->synchroniseTable($table, $force)) {
                $this->erroneousTables++;
            }
        }
    }

    private function getTable(string $tableHandle): Table
    {
        try {
            return $this->tableRepository->findByHandle($tableHandle);
        } catch (TableNotFoundException) {
            $message = \sprintf('Table with handle "%s" not available (perhaps disabled?)!', $tableHandle);
            $this->logger->emergency($message);

            throw new SynchronisationException($message, 1567003394);
        }
    }
}
