<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Domain\Entity;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Enumerations\TableType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TableTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $actual = Table::fromArray([
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
            'disabled' => '1',
        ]);

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
        self::assertTrue($actual->disabled);
    }
}
