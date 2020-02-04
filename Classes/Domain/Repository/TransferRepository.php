<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Domain\Repository;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;

class TransferRepository extends Repository
{
    protected const TABLE = 'tx_jobrouterdata_domain_model_transfer';

    public function countErroneousTransmissions(): int
    {
        $query = $this->createQuery();

        return $query
            ->matching(
                $query->logicalAnd([
                    $query->equals('transmitSuccess', 0),
                    $query->greaterThan('transmitDate', 0),
                ])
            )
            ->execute()
            ->count();
    }

    public function findFirstTransmitDate()
    {
        return $this->findTransmitDateByOrder();
    }

    public function findLastTransmitDate()
    {
        return $this->findTransmitDateByOrder('DESC');
    }

    protected function findTransmitDateByOrder(string $order = 'ASC')
    {
        $queryBuilder = $this->objectManager->get(ConnectionPool::class)
            ->getQueryBuilderForTable(static::TABLE);

        $statement = $queryBuilder
            ->select('transmit_date')
            ->from(static::TABLE)
            ->where($queryBuilder->expr()->gt('transmit_date', 0))
            ->orderBy('transmit_date', $order)
            ->setMaxResults(1)
            ->execute();

        return $statement->fetchColumn(0);
    }
}
