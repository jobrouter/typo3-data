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
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @internal
 */
class SynchronisationRunner implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var TableRepository
     */
    private $tableRepository;

    /**
     * @var SimpleTableSynchroniser
     */
    private $simpleTableSynchroniser;

    /**
     * @var CustomTableSynchroniser
     */
    private $customTableSynchroniser;

    private $totalNumberOfTables = 0;
    private $erroneousNumberOfTables = 0;

    public function __construct(
        CustomTableSynchroniser $customTableSynchroniser,
        SimpleTableSynchroniser $simpleTableSynchroniser,
        TableRepository $tableRepository
    ) {
        $this->customTableSynchroniser = $customTableSynchroniser;
        $this->simpleTableSynchroniser = $simpleTableSynchroniser;
        $this->tableRepository = $tableRepository;
    }

    public function run(string $tableHandle): array
    {
        if ($tableHandle === '') {
            $tables = $this->tableRepository->findAll();
            /** @var Table $table */
            foreach ($tables as $table) {
                $this->synchroniseTable($table);
            }
        } else {
            $this->synchroniseTable($this->getTable($tableHandle));
        }

        return [
            $this->totalNumberOfTables,
            $this->erroneousNumberOfTables,
        ];
    }

    private function synchroniseTable(Table $table): void
    {
        switch ($table->getType()) {
            case Table::TYPE_SIMPLE:
                $this->totalNumberOfTables++;
                if ($this->simpleTableSynchroniser->synchroniseTable($table) === false) {
                    $this->erroneousNumberOfTables++;
                }
                break;

            case Table::TYPE_CUSTOM_TABLE:
                $this->totalNumberOfTables++;
                if ($this->customTableSynchroniser->synchroniseTable($table) === false) {
                    $this->erroneousNumberOfTables++;
                }
                break;

            case Table::TYPE_OTHER_USAGE:
            case Table::TYPE_FORM_FINISHER:
                // do nothing
                break;

            default:
                $message = \sprintf('Table with uid "%d" has invalid type "%d"!', $table->getUid(), $table->getType());
                $this->logger->error($message);
                $this->erroneousNumberOfTables++;
        }
    }

    private function getTable(string $tableHandle): Table
    {
        $table = $this->tableRepository->findByHandle($tableHandle);

        if (! $table instanceof Table) {
            $message = \sprintf('Table with handle "%s" not available (perhaps disabled?)!', $tableHandle);
            $this->logger->emergency($message);

            throw new SynchronisationException($message, 1567003394);
        }

        return $table;
    }
}
