<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class TransferRepository extends Repository
{
    /**
     * @return mixed[]|QueryResultInterface
     */
    public function findErroneousTransfers()
    {
        $query = $this->createQuery();

        return $query
            ->matching(
                $query->logicalAnd([
                    $query->equals('transmitSuccess', 0),
                    $query->logicalNot(
                        $query->equals('transmitMessage', '')
                    ),
                ])
            )
            ->setOrderings([
                'crdate' => QueryInterface::ORDER_ASCENDING,
            ])
            ->execute();
    }
}
