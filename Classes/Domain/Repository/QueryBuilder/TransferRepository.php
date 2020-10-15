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
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function countGroupByTransmitSuccess(): array
    {
        return $this->queryBuilder
            ->select('transmit_success')
            ->addSelectLiteral('COUNT(*) AS ' . $this->queryBuilder->quoteIdentifier('count'))
            ->from('tx_jobrouterdata_domain_model_transfer')
            ->groupBy('transmit_success')
            ->execute()
            ->fetchAll();
    }

    public function countTransmitFailed(): int
    {
        $whereExpressions = [
            $this->queryBuilder->expr()->eq(
                'transmit_success',
                $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
            $this->queryBuilder->expr()->gt(
                'transmit_date',
                $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
        ];

        $count = $this->queryBuilder
            ->count('*')
            ->from('tx_jobrouterdata_domain_model_transfer')
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
        return $this->queryBuilder
            ->delete('tx_jobrouterdata_domain_model_transfer')
            ->where(
                $this->queryBuilder->expr()->eq(
                    'transmit_success',
                    $this->queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                ),
                $this->queryBuilder->expr()->lt(
                    'crdate',
                    $this->queryBuilder->createNamedParameter($maximumTimestampForDeletion, \PDO::PARAM_INT)
                )
            )
            ->execute();
    }
}
