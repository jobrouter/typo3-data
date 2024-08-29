<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Repository;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Transfer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 * Set public for functional tests
 */
#[Autoconfigure(public: true)]
class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_transfer';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * @return Transfer[]
     */
    public function findNotTransmitted(): array
    {
        $result = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'transmit_success' => 0,
                ],
            );

        $transfers = [];
        while ($row = $result->fetchAssociative()) {
            $transfers[] = Transfer::fromArray($row);
        }

        return $transfers;
    }

    /**
     * @return Transfer[]
     */
    public function findErroneousTransfers(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('transmit_success', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('transmit_message', $queryBuilder->createNamedParameter('')),
            )
            ->orderBy('crdate', 'ASC')
            ->executeQuery();

        $transfers = [];
        while ($row = $result->fetchAssociative()) {
            $transfers[] = Transfer::fromArray($row);
        }

        return $transfers;
    }

    public function add(int $tableUid, string $correlationId, string $data, \DateTimeInterface $date): int
    {
        return $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->insert(
                self::TABLE_NAME,
                [
                    'crdate' => $date->getTimestamp(),
                    'table_uid' => $tableUid,
                    'correlation_id' => $correlationId,
                    'data' => $data,
                ],
                [
                    Connection::PARAM_INT,
                    Connection::PARAM_INT,
                    Connection::PARAM_STR,
                    Connection::PARAM_STR,
                ],
            );
    }

    public function updateTransmitData(int $uid, bool $success, \DateTimeImmutable $date, string $message): int
    {
        return $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->update(
                self::TABLE_NAME,
                [
                    'transmit_success' => (int) $success,
                    'transmit_date' => $date->getTimestamp(),
                    'transmit_message' => $message,
                ],
                [
                    'uid' => $uid,
                ],
                [
                    Connection::PARAM_INT,
                    Connection::PARAM_INT,
                    Connection::PARAM_STR,
                    Connection::PARAM_INT,
                ],
            );
    }

    /**
     * @return list<array<string, mixed>>
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
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT),
            ),
            $queryBuilder->expr()->gt(
                'transmit_date',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT),
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
                    $queryBuilder->createNamedParameter(1, Connection::PARAM_INT),
                ),
                $queryBuilder->expr()->lt(
                    'crdate',
                    $queryBuilder->createNamedParameter($maximumTimestampForDeletion, Connection::PARAM_INT),
                ),
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
