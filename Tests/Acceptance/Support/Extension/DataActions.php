<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Acceptance\Support\Extension;

use Brotkrueml\JobRouterData\Extension;
use Psr\Http\Client\ClientInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Testbase;

trait DataActions
{
    public function truncateDataTables(): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $connection->truncate('tx_jobrouterdata_domain_model_table');
        $connection->truncate('tx_jobrouterdata_domain_model_column');
        $connection->truncate('tx_jobrouterdata_domain_model_dataset');
        $connection->truncate('tx_jobrouterdata_domain_model_transfer');
    }

    public function importXmlDatabaseFixture(string $fixtureFileName): void
    {
        $fixturePath = \sprintf(
            'EXT:%s/Tests/Acceptance/Fixtures/%s',
            Extension::KEY,
            $fixtureFileName,
        );

        (new TestBase())->importXmlDatabaseFixture($fixturePath);
    }

    public function createMockServerExpectationForConnection(): void
    {
        $body = [
            'httpRequest' => [
                'method' => 'POST',
                'path' => '/api/rest/v2/application/tokens',
                'body' => \sprintf(
                    '{"username":"%s","password":"%s","lifetime":600}',
                    'john.doe',
                    'secretPwd',
                ),
            ],
            'httpResponse' => [
                'statusCode' => 201,
                'headers' => [
                    'content-type' => ['application/json'],
                ],
                'body' => '{"tokens":["testtoken"]}',
            ],
            'times' => [
                'remainingTimes' => 1,
            ],
        ];

        $content = new Stream('php://temp', 'rw');
        $content->write(\json_encode($body));

        $expectationUrl = 'http://mockserver:1080/mockserver/expectation';
        $request = (new RequestFactory())
            ->createRequest('PUT', $expectationUrl)
            ->withBody($content);

        $client = GeneralUtility::makeInstance(ClientInterface::class);
        $client->sendRequest($request);
    }

    public function createMockServerExpectationForGetJobDataDataSets(string $tableGuid): void
    {
        $body = [
            'httpRequest' => [
                'method' => 'GET',
                'path' => \sprintf('/api/rest/v2/application/jobdata/tables/%s/datasets', $tableGuid),
            ],
            'httpResponse' => [
                'statusCode' => 200,
                'headers' => [
                    'content-type' => ['application/json'],
                ],
                'body' => '{"datasets":[]}',
            ],
            'times' => [
                'remainingTimes' => 1,
            ],
        ];

        $content = new Stream('php://temp', 'rw');
        $content->write(\json_encode($body));

        $expectationUrl = 'http://mockserver:1080/mockserver/expectation';
        $request = (new RequestFactory())
            ->createRequest('PUT', $expectationUrl)
            ->withBody($content);

        $client = GeneralUtility::makeInstance(ClientInterface::class);
        $client->sendRequest($request);
    }
}
