<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Repository\JobRouter;

use Brotkrueml\JobRouterClient\Client\ClientInterface;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\ConnectionNotAvailableException;
use Brotkrueml\JobRouterData\Exception\DatasetsNotAvailableException;
use Brotkrueml\JobRouterData\Exception\TableNotAvailableException;

class JobDataRepository
{
    protected const RESOURCE_TEMPLATE_DELETE = 'application/jobdata/tables/%s/datasets';
    protected const RESOURCE_TEMPLATE_GET_ALL = 'application/jobdata/tables/%s/datasets';
    protected const RESOURCE_TEMPLATE_GET_JRID = 'application/jobdata/tables/%s/datasets/%d';
    protected const RESOURCE_TEMPLATE_POST = 'application/jobdata/tables/%s/datasets';
    protected const RESOURCE_TEMPLATE_PUT = 'application/jobdata/tables/%s/datasets/%d';

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var ClientInterface
     */
    protected $client;

    public function __construct(
        RestClientFactory $restClientFactory,
        TableRepository $tableRepository,
        string $tableHandle
    ) {
        $this->initialiseClient($restClientFactory, $tableRepository, $tableHandle);
    }

    private function initialiseClient(
        RestClientFactory $restClientFactory,
        TableRepository $tableRepository,
        string $tableHandle
    ): void {
        $this->table = $tableRepository->findOneByHandle($tableHandle);
        if (!$this->table) {
            throw new TableNotAvailableException(
                \sprintf('Table with handle "%s" is not available!', $tableHandle),
                1595951023
            );
        }

        $connection = $this->table->getConnection();
        if (!$connection) {
            throw new ConnectionNotAvailableException(
                \sprintf('Connection for table with handle "%s" is not available!', $tableHandle),
                1595951024
            );
        }

        $this->client = $restClientFactory->create($connection);
    }

    public function add(array $dataset): array
    {
        $response = $this->client->request(
            'POST',
            \sprintf(self::RESOURCE_TEMPLATE_POST, $this->table->getTableGuid()),
            ['dataset' => $dataset]
        );

        return $this->buildDatasetsArrayFromJson($response->getBody()->getContents());
    }

    public function remove(int ...$jrids): void
    {
        $datasets = [];
        foreach ($jrids as $jrid) {
            $datasets[] = ['jrid' => $jrid];
        }

        $this->client->request(
            'DELETE',
            \sprintf(self::RESOURCE_TEMPLATE_DELETE, $this->table->getTableGuid()),
            ['datasets' => $datasets]
        );
    }

    public function update(int $jrid, array $dataset): array
    {
        $response = $this->client->request(
            'PUT',
            \sprintf(self::RESOURCE_TEMPLATE_PUT, $this->table->getTableGuid(), $jrid),
            ['dataset' => $dataset]
        );

        return $this->buildDatasetsArrayFromJson($response->getBody()->getContents());
    }

    public function findAll(): array
    {
        $response = $this->client->request(
            'GET',
            \sprintf(self::RESOURCE_TEMPLATE_GET_ALL, $this->table->getTableGuid())
        );

        return $this->buildDatasetsArrayFromJson($response->getBody()->getContents());
    }

    public function findByJrid(int $jrid): array
    {
        $response = $this->client->request(
            'GET',
            \sprintf(self::RESOURCE_TEMPLATE_GET_JRID, $this->table->getTableGuid(), $jrid)
        );

        return $this->buildDatasetsArrayFromJson($response->getBody()->getContents());
    }

    protected function buildDatasetsArrayFromJson(string $json): array
    {
        $decodedJson = \json_decode($json, true) ?? [];
        if (!\array_key_exists('datasets', $decodedJson)) {
            throw new DatasetsNotAvailableException(
                \sprintf('Key "datasets" is not available in response, given: %s', $json),
                1595954069
            );
        }

        return $decodedJson['datasets'];
    }
}
