<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Entity\Table;
use Brotkrueml\JobRouterData\Enumerations\TableType;
use Brotkrueml\JobRouterData\Exception\TableNotFoundException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

class TableRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_table';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
    }

    /**
     * @return Table[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->executeQuery();

        $tables = [];
        while ($row = $result->fetchAssociative()) {
            $tables[] = Table::fromArray($row);
        }

        return $tables;
    }

    /**
     * @return Table[]
     */
    public function findAllByTypeWithHidden(TableType $type): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter($type->value, Connection::PARAM_INT))
            )
            ->orderBy('disabled', 'ASC')
            ->addOrderBy('name', 'ASC')
            ->executeQuery();

        $tables = [];
        while ($row = $result->fetchAssociative()) {
            $tables[] = Table::fromArray($row);
        }

        return $tables;
    }

    public function findByUid(int $uid): Table
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw TableNotFoundException::forUid($uid);
        }

        return Table::fromArray($row);
    }

    public function findByUidWithHidden(int $uid): Table
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw TableNotFoundException::forUid($uid);
        }

        return Table::fromArray($row);
    }

    public function findByHandle(string $handle): Table
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('handle', $queryBuilder->createNamedParameter($handle))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw TableNotFoundException::forHandle($handle);
        }

        return Table::fromArray($row);
    }
}
