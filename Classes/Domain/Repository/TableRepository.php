<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Enumerations\TableType;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for tables
 */
class TableRepository extends Repository
{
    /**
     * @var array<string, string>
     * @phpstan-ignore-next-line
     */
    protected $defaultOrderings = [
        'disabled' => QueryInterface::ORDER_ASCENDING,
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return mixed[]|QueryResultInterface<Table>
     */
    public function findAllByTypeWithHidden(TableType $type): array|QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->matching($query->equals('type', $type->value));

        return $query->execute();
    }

    public function findByIdentifierWithHidden(int $identifier): ?object
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->matching($query->equals('uid', $identifier));

        return $query->execute()->getFirst();
    }

    /**
     * @return mixed[]|QueryResultInterface<Table>
     */
    public function findAllSyncTables(): array|QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr([
                $query->equals('type', TableType::Simple->value),
                $query->equals('type', TableType::CustomTable->value),
            ])
        );

        return $query->execute();
    }

    public function findByHandle(string $handle): ?Table
    {
        $query = $this->createQuery();
        $query->matching($query->equals('handle', $handle));

        return $query->execute()->getFirst();
    }
}
