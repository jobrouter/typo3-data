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
class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_transfer';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
    }

    /**
     * @return list<array{transmit_success: int, count: int}>
     */
    public function countGroupByTransmitSuccess(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('transmit_success')
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->groupBy('transmit_success')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function countTransmitFailed(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $whereExpressions = [
            $queryBuilder->expr()->eq(
                'transmit_success',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
            ),
            $queryBuilder->expr()->gt(
                'transmit_date',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
            ),
        ];

        return $queryBuilder
            ->count('*')
            ->from(self::TABLE_NAME)
            ->where(...$whereExpressions)
            ->executeQuery()
            ->fetchOne();
    }

    public function deleteOldSuccessfulTransfers(int $maximumTimestampForDeletion): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->delete(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'transmit_success',
                    $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->lt(
                    'crdate',
                    $queryBuilder->createNamedParameter($maximumTimestampForDeletion, Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }

    public function findFirstCreationDate(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->selectLiteral(\sprintf('MIN(%s)', $queryBuilder->quoteIdentifier('crdate')))
            ->from(self::TABLE_NAME)
            ->executeQuery()
            ->fetchOne() ?: 0;
    }
}
