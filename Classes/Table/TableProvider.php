<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Table;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
class TableProvider
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * @return string[]
     */
    public function getCustomTables(): array
    {
        $connection = $this->connectionPool->getConnectionByName('Default');

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->createSchemaManager();

        $customTables = [];
        foreach ($schemaManager->listTableNames() as $tableName) {
            if (! \str_starts_with($tableName, 'tx_')) {
                continue;
            }

            if ($tableName === 'tx_jobrouterdata_domain_model_dataset') {
                continue;
            }

            if (\array_key_exists('jrid', $schemaManager->listTableColumns($tableName))) {
                $customTables[] = $tableName;
            }
        }

        return $customTables;
    }

    /**
     * @return string[]
     */
    public function getColumnsForCustomTable(string $customTable): array
    {
        $connection = $this->connectionPool->getConnectionForTable($customTable);

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->createSchemaManager();

        return \array_keys($schemaManager->listTableColumns($customTable));
    }
}
