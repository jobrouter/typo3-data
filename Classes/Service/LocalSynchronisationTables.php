<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Service;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class LocalSynchronisationTables
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->connectionPool = $objectManager->get(ConnectionPool::class);
    }

    public function getLocalTables(array $config): array
    {
        $connection = $this->connectionPool->getConnectionForTable('tx_jobrouterdata_domain_model_table');

        $alreadyAssignedLocalTables = \array_column(
            $connection->fetchAll(
                'SELECT local_table FROM tx_jobrouterdata_domain_model_table WHERE local_table != ""'
            ),
            'local_table'
        );

        $alreadyAssignedLocalTables = \array_diff(
            $alreadyAssignedLocalTables,
            [$config['row']['local_table']]
        );

        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();

        $tables = [];
        foreach ($schemaManager->listTableNames() as $tableName) {
            if (\strpos($tableName, 'tx_') !== 0) {
                continue;
            }

            if ($tableName === 'tx_jobrouterdata_domain_model_dataset') {
                continue;
            }

            if (\in_array($tableName, $alreadyAssignedLocalTables)) {
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
