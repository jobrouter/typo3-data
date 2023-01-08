<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
use Brotkrueml\JobRouterData\Domain\Entity\Column;
use Brotkrueml\JobRouterData\Domain\Entity\Table;
use Brotkrueml\JobRouterData\Enumerations\TableType;
use PHPUnit\Framework\TestCase;

final class TableTest extends TestCase
{
    /**
     * @test
     */
    public function fromArray(): void
    {
        $actual = Table::fromArray($this->buildArray());

        self::assertInstanceOf(Table::class, $actual);
        self::assertSame(42, $actual->uid);
        self::assertSame(1, $actual->connectionUid);
        self::assertSame(TableType::CustomTable, $actual->type);
        self::assertSame('some_handle', $actual->handle);
        self::assertSame('some name', $actual->name);
        self::assertSame('some guid', $actual->tableGuid);
        self::assertSame('some_custom_table', $actual->customTable);
        self::assertSame('some sync hash', $actual->datasetsSyncHash);
        self::assertSame(1234567890, $actual->lastSyncDate->getTimestamp());
        self::assertSame('some sync error', $actual->lastSyncError);
        self::assertNull($actual->connection);
        self::assertNull($actual->columns);
    }

    /**
     * @test
     */
    public function withConnection(): void
    {
        $table = Table::fromArray($this->buildArray());
        $connection = $this->buildConnection();

        $actual = $table->withConnection($connection);

        self::assertInstanceOf(Table::class, $actual);
        self::assertSame(42, $actual->uid);
        self::assertSame(1, $actual->connectionUid);
        self::assertSame(TableType::CustomTable, $actual->type);
        self::assertSame('some_handle', $actual->handle);
        self::assertSame('some name', $actual->name);
        self::assertSame('some guid', $actual->tableGuid);
        self::assertSame('some_custom_table', $actual->customTable);
        self::assertSame('some sync hash', $actual->datasetsSyncHash);
        self::assertSame(1234567890, $actual->lastSyncDate->getTimestamp());
        self::assertSame('some sync error', $actual->lastSyncError);
        self::assertSame($connection, $actual->connection);
        self::assertNull($actual->columns);
    }

    /**
     * @test
     */
    public function withColumns(): void
    {
        $table = Table::fromArray($this->buildArray());
        $columns = [
            $this->buildColumn(1),
            $this->buildColumn(2),
        ];

        $actual = $table->withColumns($columns);

        self::assertInstanceOf(Table::class, $actual);
        self::assertSame(42, $actual->uid);
        self::assertSame(1, $actual->connectionUid);
        self::assertSame(TableType::CustomTable, $actual->type);
        self::assertSame('some_handle', $actual->handle);
        self::assertSame('some name', $actual->name);
        self::assertSame('some guid', $actual->tableGuid);
        self::assertSame('some_custom_table', $actual->customTable);
        self::assertSame('some sync hash', $actual->datasetsSyncHash);
        self::assertSame(1234567890, $actual->lastSyncDate->getTimestamp());
        self::assertSame('some sync error', $actual->lastSyncError);
        self::assertNull($actual->connection);
        self::assertSame($columns, $actual->columns);
    }

    /**
     * @test
     */
    public function withConnectionAndWithColumns(): void
    {
        $table = Table::fromArray($this->buildArray());
        $connection = $this->buildConnection();
        $columns = [$this->buildColumn(1)];

        $actual = $table
            ->withConnection($connection)
            ->withColumns($columns);

        self::assertSame($connection, $actual->connection);
        self::assertSame($columns, $actual->columns);
    }

    /**
     * @test
     */
    public function withColumnsAndWithConnection(): void
    {
        $table = Table::fromArray($this->buildArray());
        $connection = $this->buildConnection();
        $columns = [$this->buildColumn(1)];

        $actual = $table
            ->withColumns($columns)
            ->withConnection($connection);

        self::assertSame($connection, $actual->connection);
        self::assertSame($columns, $actual->columns);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildArray(): array
    {
        return [
            'uid' => '42',
            'connection' => '1',
            'type' => TableType::CustomTable->value,
            'handle' => 'some_handle',
            'name' => 'some name',
            'table_guid' => 'some guid',
            'custom_table' => 'some_custom_table',
            'datasets_sync_hash' => 'some sync hash',
            'last_sync_date' => '1234567890',
            'last_sync_error' => 'some sync error',
        ];
    }

    private function buildConnection(): Connection
    {
        return Connection::fromArray([
            'uid' => 1,
            'name' => '',
            'handle' => '',
            'base_url' => '',
            'username' => '',
            'password' => '',
            'timeout' => 0,
            'verify' => true,
            'proxy' => '',
            'jobrouter_version' => '',
            'disabled' => false,
        ]);
    }

    private function buildColumn(int $uid): Column
    {
        return Column::fromArray([
            'uid' => $uid,
            'name' => '',
            'label' => '',
            'type' => 0,
            'decimal_places' => 0,
            'field_size' => 0,
            'alignment' => '',
            'sorting_priority' => 0,
            'sorting_order' => '',
        ]);
    }
}
