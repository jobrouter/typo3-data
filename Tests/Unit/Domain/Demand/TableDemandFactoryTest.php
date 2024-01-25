<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Domain\Demand;

use JobRouter\AddOn\Typo3Connector\Domain\Entity\Connection;
use JobRouter\AddOn\Typo3Connector\Domain\Repository\ConnectionRepository;
use JobRouter\AddOn\Typo3Connector\Exception\ConnectionNotFoundException;
use JobRouter\AddOn\Typo3Data\Domain\Demand\TableDemandFactory;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Column;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Domain\Repository\ColumnRepository;
use JobRouter\AddOn\Typo3Data\Enumerations\TableType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class TableDemandFactoryTest extends TestCase
{
    private ColumnRepository&Stub $columnRepositoryStub;
    private ConnectionRepository&Stub $connectionRepositoryStub;
    private TableDemandFactory $subject;

    protected function setUp(): void
    {
        $this->columnRepositoryStub = $this->createStub(ColumnRepository::class);
        $this->connectionRepositoryStub = $this->createStub(ConnectionRepository::class);

        $this->subject = new TableDemandFactory($this->columnRepositoryStub, $this->connectionRepositoryStub);
    }

    #[Test]
    public function createWithAvailableConnectionAndWithoutDisabled(): void
    {
        $table = $this->getTable();
        $connection = $this->getConnection();

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(2)
            ->willReturn($connection);
        $this->columnRepositoryStub
            ->method('findByTableUid')
            ->with(1)
            ->willReturn([]);

        $actual = $this->subject->create($table);

        self::assertSame(1, $actual->uid);
        self::assertSame($connection, $actual->connection);
        self::assertSame(TableType::Simple, $actual->type);
        self::assertSame('some_handle', $actual->handle);
        self::assertSame('Some name', $actual->name);
        self::assertSame('some table guid', $actual->tableGuid);
        self::assertSame('some_custom_table', $actual->customTable);
        self::assertSame('some_hash', $actual->datasetsSyncHash);
        self::assertNull($actual->lastSyncDate);
        self::assertSame('some error', $actual->lastSyncError);
    }

    #[Test]
    public function createWithAvailableConnectionAndWithDisabled(): void
    {
        $table = $this->getTable();
        $connection = $this->getConnection();

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(2, true)
            ->willReturn($connection);
        $this->columnRepositoryStub
            ->method('findByTableUid')
            ->with(1)
            ->willReturn([]);

        $actual = $this->subject->create($table, true);

        self::assertSame(1, $actual->uid);
    }

    #[Test]
    public function createWithUnavailableConnection(): void
    {
        $table = $this->getTable();

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(2)
            ->willThrowException(new ConnectionNotFoundException());
        $this->columnRepositoryStub
            ->method('findByTableUid')
            ->with(1)
            ->willReturn([]);

        $actual = $this->subject->create($table);

        self::assertNull($actual->connection);
    }

    #[Test]
    public function createWithColumns(): void
    {
        $table = $this->getTable();

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(2)
            ->willReturn($this->getConnection());
        $this->columnRepositoryStub
            ->method('findByTableUid')
            ->with(1)
            ->willReturn([Column::fromArray([
                'uid' => 3,
                'name' => '',
                'label' => '',
                'type' => 1,
                'decimal_places' => 0,
                'field_size' => 0,
                'alignment' => '',
                'sorting_priority' => 1,
                'sorting_order' => '',
            ])]);

        $actual = $this->subject->create($table);

        self::assertCount(1, $actual->columns);
        self::assertSame(3, $actual->columns[0]->uid);
    }

    #[Test]
    public function createMultiple(): void
    {
        $table1 = $this->getTable();
        $table2 = $this->getTable(2);

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(2)
            ->willReturn($this->getConnection());
        $this->columnRepositoryStub
            ->method('findByTableUid')
            ->willReturn([]);

        $actual = $this->subject->createMultiple([$table1, $table2]);

        self::assertCount(2, $actual);
        self::assertSame(1, $actual[0]->uid);
        self::assertSame(2, $actual[1]->uid);
    }

    private function getTable(int $uid = 1): Table
    {
        return Table::fromArray([
            'uid' => $uid,
            'connection' => 2,
            'type' => 1,
            'handle' => 'some_handle',
            'name' => 'Some name',
            'table_guid' => 'some table guid',
            'custom_table' => 'some_custom_table',
            'datasets_sync_hash' => 'some_hash',
            'last_sync_date' => null,
            'last_sync_error' => 'some error',
        ]);
    }

    private function getConnection(): Connection
    {
        return Connection::fromArray([
            'uid' => 2,
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
}
