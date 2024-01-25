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
final class TableDemand
{
    /**
     * @param Column[] $columns
     */
    public function __construct(
        public readonly int $uid,
        public readonly ?Connection $connection,
        public readonly TableType $type,
        public readonly string $handle,
        public readonly string $name,
        public readonly string $tableGuid,
        public readonly string $customTable,
        public readonly string $datasetsSyncHash,
        public readonly ?\DateTimeImmutable $lastSyncDate,
        public readonly string $lastSyncError,
        public readonly array $columns,
    ) {}
}
