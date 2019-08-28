<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Synchroniser;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterConnector\Service\Rest;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TableSynchroniser
{
    /** @var TableRepository */
    private $tableRepository;

    /** @var ConnectionPool */
    private $connectionPool;

    /** @var Logger */
    private $logger;

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->tableRepository = $objectManager->get(TableRepository::class);
        $this->connectionPool = $objectManager->get(ConnectionPool::class);

        $this->logger = $objectManager->get(LogManager::class)->getLogger(__CLASS__);
    }

    public function synchronise(?string $name): void
    {
        if ($name === null) {
            $tables = $this->tableRepository->findAll();
            /** @var Table $table */
            foreach ($tables as $table) {
                $this->synchroniseTable($table);
            }
        } else {
            $this->synchroniseTable($this->determineTable($name));
        }
    }

    private function synchroniseTable(Table $table): void
    {
        $this->logger->info('Started synchronising JobData table', ['table name' => $table->getName()]);

        $datasets = $this->retrieveDatasetsFromJobRouter($table);
        $this->storeDatasets($table, $datasets);

        $this->logger->info('Ended synchronising JobData table', ['table name' => $table->getName()]);
    }

    private function determineTable(string $name): Table
    {
        $table = $this->tableRepository->findOneByName($name);

        if (!$table instanceof Table) {
            $message = \sprintf('Table with name "%s" not available (perhaps disabled?)!', $name);
            $this->logger->emergency($message);

            throw new SynchronisationException($message, 1567003394);
        }

        return $table;
    }

    private function retrieveDatasetsFromJobRouter(Table $table): array
    {
        $restClient = (new Rest())->getRestClient($table->getConnection());

        $response = $restClient->request(
            \sprintf('application/jobdata/tables/%s/datasets', $table->getTableGuid()),
            'GET'
        );

        $responseContent = \json_decode($response->getContent(), true);

        if ($responseContent === null) {
            $message = 'Content of response is no valid JSON!';
            $this->logger->emergency($message, ['received content' => $response->getContent()]);

            throw new SynchronisationException($message, 1567004495);
        }

        return $responseContent['datasets'];
    }

    private function storeDatasets(Table $table, array $datasets): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tx_jobrouterdata_domain_model_dataset');
        $connection->setAutoCommit(false);
        $connection->beginTransaction();

        try {
            $connection->delete(
                'tx_jobrouterdata_domain_model_dataset',
                ['table_uid' => $table->getUid()],
                ['table_uid' => \PDO::PARAM_INT]
            );

            $this->logger->debug('Deleted table records in transaction', ['table_uid' => $table->getUid()]);

            foreach ($datasets as $dataset) {
                $jrid = $dataset['jrid'];
                unset($dataset['jrid']);

                $data = [
                    'table_uid' => $table->getUid(),
                    'jrid' => $jrid,
                    'dataset' => \json_encode($dataset, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ];

                $connection->insert(
                    'tx_jobrouterdata_domain_model_dataset',
                    $data,
                    [
                        'table_uid' => \PDO::PARAM_INT,
                        'jrid' => \PDO::PARAM_INT,
                        'dataset' => \PDO::PARAM_STR,
                    ]
                );

                $this->logger->debug('Inserted table record in transaction', $data);
            }
        } catch (\Exception $e) {
            $connection->rollBack();

            $this->logger->emergency(
                'Error while synchronising, rollback',
                ['table_uid' => $table->getUid(), 'message' => $e->getMessage()]
            );

            throw new SynchronisationException($e->getMessage(), 1567014608, $e);
        }

        $connection->commit();
        $connection->setAutoCommit(true);
    }
}
