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
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
class SynchronisationService
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly RestClientFactory $restClientFactory,
        private readonly TableRepository $tableRepository
    ) {
    }

    /**
     * @return list<array<string, string|int|float|bool|null>>
     */
    public function retrieveDatasetsFromJobDataTable(Table $table): array
    {
        return (new JobDataRepository($this->restClientFactory, $this->tableRepository, $table->getHandle()))
            ->findAll();
    }

    /**
     * @param list<array<string, string|int|float|bool|null>> $datasets
     */
    public function hashDatasets(array $datasets): string
    {
        return \sha1(\json_encode($datasets, \JSON_THROW_ON_ERROR));
    }

    public function updateSynchronisationStatus(Table $table, ?string $datasetsHash = null, string $error = ''): void
    {
        // @phpstan-ignore-next-line
        $data = [
            'last_sync_date' => \time(),
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

        $tableConnection = $this->connectionPool->getConnectionForTable('tx_jobrouterdata_domain_model_table');
        $tableConnection->update(
            'tx_jobrouterdata_domain_model_table',
            $data,
            [
                'uid' => $table->getUid(),
            ],
            $types
        );
    }
}
