<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @internal
 */
class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_transfer';

    private QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return mixed[]
     */
    public function countGroupByTransmitSuccess(): array
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('transmit_success')
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->groupBy('transmit_success')
            ->execute()
            ->fetchAll();
    }

    public function countTransmitFailed(): int
    {
        $queryBuilder = $this->createQueryBuilder();

        $whereExpressions = [
            $queryBuilder->expr()->eq(
                'transmit_success',
                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
            $queryBuilder->expr()->gt(
                'transmit_date',
                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
        ];

        $count = $queryBuilder
            ->count('*')
            ->from(self::TABLE_NAME)
            ->where(...$whereExpressions)
            ->execute()
            ->fetchColumn();

        if ($count === false) {
            return 0;
        }

        return $count;
    }

    public function deleteOldSuccessfulTransfers(int $maximumTimestampForDeletion): int
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->delete(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'transmit_success',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->lt(
                    'crdate',
                    $queryBuilder->createNamedParameter($maximumTimestampForDeletion, \PDO::PARAM_INT)
                )
            )
            ->execute();
    }

    public function findFirstCreationDate(): int
    {
        $queryBuilder = $this->createQueryBuilder();

        $quotedCrdate = $queryBuilder->quoteIdentifier('crdate');

        return $queryBuilder
            ->selectLiteral(\sprintf('MIN(%s)', $quotedCrdate))
            ->from(self::TABLE_NAME)
            ->execute()
            ->fetchColumn() ?: 0;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }
}
