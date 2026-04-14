<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Entity;

use JobRouter\AddOn\Typo3Data\Enumerations\TableType;

final readonly class Table
{
    private function __construct(
        public int $uid,
        public int $connectionUid,
        public TableType $type,
        public string $handle,
        public string $name,
        public string $tableGuid,
        public string $customTable,
        public string $datasetsSyncHash,
        public ?\DateTimeImmutable $lastSyncDate,
        public string $lastSyncError,
        public bool $disabled,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $lastSyncDate = (int) $data['last_sync_date'];

        return new self(
            (int) $data['uid'],
            (int) $data['connection'],
            TableType::from($data['type']),
            $data['handle'],
            $data['name'],
            $data['table_guid'],
            $data['custom_table'],
            $data['datasets_sync_hash'],
            $lastSyncDate > 0 ? (new \DateTimeImmutable())->setTimestamp($lastSyncDate) : null,
            (string) $data['last_sync_error'],
            (bool) $data['disabled'],
        );
    }
}
