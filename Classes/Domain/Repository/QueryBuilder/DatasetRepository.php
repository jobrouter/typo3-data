<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
class DatasetRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_dataset';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
    }

    public function deleteByTableUid(int $tableUid): int
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_NAME);

        return $connection->delete(
            'tx_jobrouterdata_domain_model_dataset',
            [
                'table_uid' => $tableUid,
            ],
            [
                'table_uid' => Connection::PARAM_INT,
            ]
        );
    }
}
