<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Entity;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
use Brotkrueml\JobRouterData\Enumerations\TableType;

final class Table
{
    /**
     * @param Column[]|null $columns
     */
    private function __construct(
        public readonly int $uid,
        public readonly int $connectionUid,
        public readonly TableType $type,
        public readonly string $handle,
        public readonly string $name,
        public readonly string $tableGuid,
        public readonly string $customTable,
        public readonly string $datasetsSyncHash,
        public readonly ?\DateTimeImmutable $lastSyncDate,
        public readonly string $lastSyncError,
        public readonly ?Connection $connection = null,
        public readonly ?array $columns = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $lastSyncDate = (int)$data['last_sync_date'];

        return new self(
            (int)$data['uid'],
            (int)$data['connection'],
            TableType::from($data['type']),
            $data['handle'],
            $data['name'],
            $data['table_guid'],
            $data['custom_table'],
            $data['datasets_sync_hash'],
            $lastSyncDate > 0 ? (new \DateTimeImmutable())->setTimestamp($lastSyncDate) : null,
            (string)$data['last_sync_error'],
        );
    }

    public function withConnection(Connection $connection): self
    {
        return new self(
            $this->uid,
            $this->connectionUid,
            $this->type,
            $this->handle,
            $this->name,
            $this->tableGuid,
            $this->customTable,
            $this->datasetsSyncHash,
            $this->lastSyncDate,
            $this->lastSyncError,
            $connection,
            $this->columns,
        );
    }

    /**
     * @param Column[] $columns
     */
    public function withColumns(array $columns): self
    {
        return new self(
            $this->uid,
            $this->connectionUid,
            $this->type,
            $this->handle,
            $this->name,
            $this->tableGuid,
            $this->customTable,
            $this->datasetsSyncHash,
            $this->lastSyncDate,
            $this->lastSyncError,
            $this->connection,
            $columns,
        );
    }
}
