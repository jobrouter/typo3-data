<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Synchronisation;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class AbstractSynchroniser
{
    /** @var ConnectionPool */
    protected $connectionPool;

    /** @var Logger */
    protected $logger;

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->connectionPool = $objectManager->get(ConnectionPool::class);

        $this->logger = $objectManager->get(LogManager::class)->getLogger(__CLASS__);
    }

    abstract public function synchroniseTable(Table $table): void;

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
}
