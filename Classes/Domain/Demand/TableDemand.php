<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Demand;

use JobRouter\AddOn\Typo3Connector\Domain\Entity\Connection;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Column;
use JobRouter\AddOn\Typo3Data\Enumerations\TableType;

/**
 * @internal
 */
final readonly class TableDemand
{
    /**
     * @param Column[] $columns
     */
    public function __construct(
        public int $uid,
        public ?Connection $connection,
        public TableType $type,
        public string $handle,
        public string $name,
        public string $tableGuid,
        public string $customTable,
        public string $datasetsSyncHash,
        public ?\DateTimeImmutable $lastSyncDate,
        public string $lastSyncError,
        public array $columns,
        public bool $disabled,
    ) {}
}
