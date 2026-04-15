<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Repository;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Content;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

final readonly class ContentRepository
{
    private const TABLE_NAME = 'tt_content';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    /**
     * @return list<Content>
     */
    public function findByFlexFormField(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $result = $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('tx_jobrouterdata_table', Connection::PARAM_STR)),
                $queryBuilder->expr()->neq('pi_flexform', $queryBuilder->createNamedParameter('', Connection::PARAM_STR)),
            )
            ->executeQuery();

        $items = [];
        while ($row = $result->fetchAssociative()) {
            $items[] = Content::fromArray($row);
        }

        return $items;
    }

    public function updateTableFieldAndResetFlexFormField(int $uid, int $tableId): void
    {
        $this->connectionPool->getConnectionForTable(self::TABLE_NAME)
            ->update(
                self::TABLE_NAME,
                [
                    'pi_flexform' => '',
                    'tx_jobrouterdata_table' => $tableId,
                ],
                [
                    'uid' => $uid,
                ],
            );
    }
}
