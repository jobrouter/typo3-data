<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Synchronisation;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class AbstractSynchroniser implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var ConnectionPool */
    protected $connectionPool;

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->connectionPool = $objectManager->get(ConnectionPool::class);
    }

    abstract public function synchroniseTable(Table $table): bool;

    protected function retrieveDatasetsFromJobRouter(Table $table): array
    {
        $restClient = (new RestClientFactory())->create($table->getConnection());

        $response = $restClient->request(
            'GET',
            \sprintf('application/jobdata/tables/%s/datasets', $table->getTableGuid())
        );

        $content = $response->getBody()->getContents();
        $responseContent = \json_decode($content, true);

        if ($responseContent === null) {
            $message = 'Content of response is no valid JSON!';
            $this->logger->emergency($message, ['received content' => $content]);

            throw new SynchronisationException($message, 1567004495);
        }

        return $responseContent['datasets'];
    }

    protected function hashDatasets(array $datasets): string
    {
        return \sha1(\serialize($datasets));
    }

    protected function updateSynchronisationStatus(Table $table, ?string $datasetsHash = null, string $error = ''): void
    {
        $data = [
            'last_sync_date' => time(),
            'last_sync_error' => $error,
        ];
        $types = [
            'last_sync_date' => \PDO::PARAM_INT,
            'last_sync_error' => \PDO::PARAM_STR,
        ];

        if ($datasetsHash) {
            $data['datasets_sync_hash'] = $datasetsHash;
            $types['datasets_sync_hash'] = \PDO::PARAM_STR;
        }

        $connection = $this->connectionPool->getConnectionForTable('tx_jobrouterdata_domain_model_table');
        $connection->update(
            'tx_jobrouterdata_domain_model_table',
            $data,
            ['uid' => $table->getUid()],
            $types
        );
    }
}
