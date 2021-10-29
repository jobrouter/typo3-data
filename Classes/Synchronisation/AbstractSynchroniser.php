<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Synchronisation;

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
abstract class AbstractSynchroniser implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var RestClientFactory
     */
    private $restClientFactory;

    /**
     * @var TableRepository
     */
    private $tableRepository;

    public function __construct(
        ConnectionPool $connectionPool,
        RestClientFactory $restClientFactory,
        TableRepository $tableRepository
    ) {
        $this->connectionPool = $connectionPool;
        $this->restClientFactory = $restClientFactory;
        $this->tableRepository = $tableRepository;
    }

    abstract public function synchroniseTable(Table $table): bool;

    /**
     * @return mixed[]
     */
    protected function retrieveDatasetsFromJobRouter(Table $table): array
    {
        return (new JobDataRepository($this->restClientFactory, $this->tableRepository, $table->getHandle()))
            ->findAll();
    }

    /**
     * @param list<array<string, string|int|float|bool|null>> $datasets
     */
    protected function hashDatasets(array $datasets): string
    {
        return \sha1(\serialize($datasets));
    }

    protected function updateSynchronisationStatus(Table $table, ?string $datasetsHash = null, string $error = ''): void
    {
        // @phpstan-ignore-next-line
        $data = [
            'last_sync_date' => time(),
            'last_sync_error' => $error,
        ];
        // @phpstan-ignore-next-line
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
            [
                'uid' => $table->getUid(),
            ],
            $types
        );
    }
}
