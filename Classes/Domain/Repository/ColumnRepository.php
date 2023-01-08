<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Entity\Column;
use TYPO3\CMS\Core\Database\ConnectionPool;

class ColumnRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_column';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @return Column[]
     */
    public function findByTableUid(int $tableUid): array
    {
        $rows = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'table_uid' => $tableUid,
                ],
                orderBy: [
                    'sorting' => 'DESC',
                ],
            )->fetchAllAssociative();

        $columns = [];
        foreach ($rows as $row) {
            $columns[] = Column::fromArray($row);
        }

        return $columns;
    }
}
