<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder;

use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
class TableRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_table';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
    }

    /**
     * @return string[]
     */
    public function findAssignedCustomTables(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('custom_table')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->neq('custom_table', $queryBuilder->createNamedParameter('')),
            )
            ->executeQuery()
            ->fetchFirstColumn();
    }
}
