<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Service;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class CustomTables
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * @return mixed[]
     */
    public function getTables(array $config): array
    {
        $connection = $this->connectionPool->getConnectionForTable('tx_jobrouterdata_domain_model_table');

        $alreadyAssignedTables = \array_column(
            $connection->fetchAll(
                'SELECT custom_table FROM tx_jobrouterdata_domain_model_table WHERE custom_table != ""'
            ),
            'custom_table'
        );

        $alreadyAssignedTables = \array_diff(
            $alreadyAssignedTables,
            [$config['row']['custom_table']]
        );

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();

        $tables = [];
        foreach ($schemaManager->listTableNames() as $tableName) {
            if (! \str_starts_with($tableName, 'tx_')) {
                continue;
            }

            if ($tableName === 'tx_jobrouterdata_domain_model_dataset') {
                continue;
            }

            if (\in_array($tableName, $alreadyAssignedTables, true)) {
                continue;
            }

            if (\array_key_exists('jrid', $schemaManager->listTableColumns($tableName))) {
                $tables[] = [$tableName, $tableName];
            }
        }

        $config['items'] = \array_merge($config['items'], $tables);

        return $config;
    }
}
