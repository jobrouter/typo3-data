<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Entity\Dataset;
use TYPO3\CMS\Core\Database\ConnectionPool;

class DatasetRepository
{
    private const TABLE_NAME = 'tx_jobrouterdata_domain_model_dataset';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
    }

    /**
     * @return Dataset[]
     */
    public function findByTableUid(int $tableUid): array
    {
        $result = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'table_uid' => $tableUid,
                ],
                orderBy: [
                    'jrid' => 'ASC',
                ]
            );

        $datasets = [];
        while ($row = $result->fetchAssociative()) {
            $datasets[] = Dataset::fromArray($row);
        }

        return $datasets;
    }
}
