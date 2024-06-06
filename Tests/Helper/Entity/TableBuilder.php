<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Helper\Entity;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Enumerations\TableType;

final class TableBuilder
{
    public function build(int $uid, string $handle = '', string $tableGuid = '', int $connection = 1): Table
    {
        return Table::fromArray([
            'uid' => $uid,
            'connection' => $connection,
            'type' => TableType::Simple->value,
            'handle' => $handle,
            'name' => '',
            'table_guid' => $tableGuid,
            'custom_table' => '',
            'datasets_sync_hash' => '',
            'last_sync_date' => 0,
            'last_sync_error' => '',
            'disabled' => '0',
        ]);
    }
}
