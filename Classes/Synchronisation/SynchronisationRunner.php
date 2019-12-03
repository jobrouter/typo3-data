<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Synchronisation;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class SynchronisationRunner
{
    /** @var TableRepository */
    private $tableRepository;

    /** @var Logger */
    private $logger;

    /** @var SimpleTableSynchroniser */
    private $simpleTableSynchroniser;

    /** @var LocalTableSynchroniser */
    private $localTableSynchroniser;

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->tableRepository = $objectManager->get(TableRepository::class);
        $this->logger = $objectManager->get(LogManager::class)->getLogger(__CLASS__);
        $this->simpleTableSynchroniser = $objectManager->get(SimpleTableSynchroniser::class);
        $this->localTableSynchroniser = $objectManager->get(LocalTableSynchroniser::class);
    }

    public function run(?int $tableUid): void
    {
        if ($tableUid === null) {
            $tables = $this->tableRepository->findAll();
            /** @var Table $table */
            foreach ($tables as $table) {
                $this->synchroniseTable($table);
            }
        } else {
            $this->synchroniseTable($this->getTable($tableUid));
        }
    }

    private function synchroniseTable(Table $table): void
    {
        if ($table->getType() === Table::TYPE_SIMPLE) {
            $this->simpleTableSynchroniser->synchroniseTable($table);
            return;
        }

        if ($table->getType() === Table::TYPE_LOCAL_TABLE) {
            $this->localTableSynchroniser->synchroniseTable($table);
            return;
        }

        if ($table->getType() === Table::TYPE_OTHER_USAGE) {
            // do nothing
            return;
        }

        $message = \sprintf('Table with uid "%d" has invalid type "%d"!', $table->getUid(), $table->getType());
        $this->logger->error($message);
    }

    private function getTable(int $tableUid): Table
    {
        $table = $this->tableRepository->findByIdentifier($tableUid);

        if (!$table instanceof Table) {
            $message = \sprintf('Table with uid "%d" not available (perhaps disabled?)!', $tableUid);
            $this->logger->emergency($message);

            throw new SynchronisationException($message, 1567003394);
        }

        return $table;
    }
}
