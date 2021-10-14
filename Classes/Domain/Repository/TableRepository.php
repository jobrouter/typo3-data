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
     */
    protected $defaultOrderings = [
        'disabled' => QueryInterface::ORDER_ASCENDING,
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return mixed[]|QueryResultInterface
     */
    public function findAllByTypeWithHidden(int $type)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->matching($query->equals('type', $type));

        return $query->execute();
    }

    public function findByIdentifierWithHidden(int $identifier): object
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->matching($query->equals('uid', $identifier));

        return $query->execute()->getFirst();
    }

    /**
     * @return mixed[]|QueryResultInterface
     */
    public function findAllSyncTables()
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr([
                $query->equals('type', Table::TYPE_SIMPLE),
                $query->equals('type', Table::TYPE_CUSTOM_TABLE),
            ])
        );

        return $query->execute();
    }

    public function findByHandle(string $handle): object
    {
        $query = $this->createQuery();
        $query->matching($query->equals('handle', $handle));

        return $query->execute()->getFirst();
    }
}
