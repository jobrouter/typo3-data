<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository\JobRouter;

use Brotkrueml\JobRouterClient\Client\RestClient;
use Brotkrueml\JobRouterClient\Configuration\ClientConfiguration;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactoryInterface;
use Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\DatasetNotAvailableException;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\ConnectionBuilder;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\TableBuilder;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class JobDataRepositoryTest extends FunctionalTestCase
{
    private const TEST_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqYXQiOjE1NzAyMjAwNzIsImp0aSI6IjhWMGtaSzJ5RzRxdGlhbjdGbGZTNUhPTGZaeGtZXC9obG1SVEV2VXIxVmwwPSIsImlzcyI6IkpvYlJvdXRlciIsIm5iZiI6MTU3MDIyMDA3MiwiZXhwIjoxNTcwMjIwMTAyLCJkYXRhIjp7InVzZXJuYW1lIjoicmVzdCJ9fQ.cbAyj36f9MhAwOMzlTEheRkHhuuIEOeb1Uy8i0KfUhU';

    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private static MockWebServer $server;
    private static RestClient $restClient;
    private static ClientConfiguration $configuration;

    private TableRepository&MockObject $tableRepositoryMock;
    private RestClientFactoryInterface&Stub $restClientFactoryStub;
    private JobDataRepository $subject;

    public static function setUpBeforeClass(): void
    {
        self::markTestSkipped('Will be reworked');

        self::$server = new MockWebServer();
        self::$server->start();

        self::$configuration = new ClientConfiguration(
            self::$server->getServerRoot() . '/',
            'fake_username',
            'fake_password',
        );

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/tokens',
            new Response(
                \sprintf('{"tokens":["%s"]}', self::TEST_TOKEN),
                [
                    'content-type' => 'application/json',
                ],
                201,
            ),
        );

        self::$restClient = new RestClient(self::$configuration);
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $connection = (new ConnectionBuilder())->build(
            1,
            (string)self::$configuration->getJobRouterSystem(),
            self::$configuration->getUsername(),
            self::$configuration->getPassword(),
        );

        $table = (new TableBuilder())->build(1, tableGuid: 'some-guid');
        $table = $table->withConnection($connection);

        $this->tableRepositoryMock = $this->getMockBuilder(TableRepository::class)
            ->disableOriginalConstructor()
            ->addMethods(['findOneByHandle'])
            ->getMock();
        $this->tableRepositoryMock
            ->method('findOneByHandle')
            ->with('some-handle')
            ->willReturn($table);

        $this->restClientFactoryStub = $this->createStub(RestClientFactoryInterface::class);
        $this->restClientFactoryStub
            ->method('create')
            ->with($connection)
            ->willReturn(self::$restClient);

        $this->subject = new JobDataRepository($this->restClientFactoryStub, $this->tableRepositoryMock, 'some-handle');
    }

    /**
     * @test
     */
    public function add(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/jobdata/tables/some-guid/datasets',
            new Response('{"datasets":{"jrid": "42","foo":"bar","qux":"qoo"}}', [], 201),
        );

        $dataset = [
            'foo' => 'bar',
            'qux' => 'qoo',
        ];

        $actual = $this->subject->add($dataset);
        $expected = \array_merge([
            'jrid' => '42',
        ], $dataset);

        self::assertSame($expected, $actual);

        $method = self::$server->getLastRequest()->getRequestMethod();
        self::assertSame('POST', $method);
    }

    /**
     * @test
     */
    public function remove(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/jobdata/tables/some-guid/datasets',
            new Response('', [], 204),
        );

        $this->subject->remove(1, 2, 3);

        $method = self::$server->getLastRequest()->getRequestMethod();
        self::assertSame('DELETE', $method);

        $input = self::$server->getLastRequest()->getInput();
        self::assertSame('{"datasets":[{"jrid":1},{"jrid":2},{"jrid":3}]}', $input);
    }

    /**
     * @test
     */
    public function update(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/jobdata/tables/some-guid/datasets/42',
            new Response('{"datasets":{"jrid": "42","foo":"bar","qux":"qoo"}}', [], 200),
        );

        $dataset = [
            'foo' => 'bar',
            'qux' => 'qoo',
        ];

        $actual = $this->subject->update(42, $dataset);
        $expected = \array_merge([
            'jrid' => '42',
        ], $dataset);

        self::assertSame($expected, $actual);

        $method = self::$server->getLastRequest()->getRequestMethod();
        self::assertSame('PUT', $method);

        $input = self::$server->getLastRequest()->getInput();
        self::assertSame('{"dataset":{"foo":"bar","qux":"qoo"}}', $input);
    }

    /**
     * @test
     */
    public function findAll(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/jobdata/tables/some-guid/datasets',
            new Response('{"datasets":[{"jrid": "42","foo":"bar","qux":"qoo"}]}', [], 200),
        );

        $actual = $this->subject->findAll();
        $expected = [[
            'jrid' => '42',
            'foo' => 'bar',
            'qux' => 'qoo',
        ]];

        self::assertSame($expected, $actual);

        $method = self::$server->getLastRequest()->getRequestMethod();
        self::assertSame('GET', $method);
    }

    /**
     * @test
     */
    public function findByJrid(): void
    {
        self::$server->setResponseOfPath(
            '/api/rest/v2/application/jobdata/tables/some-guid/datasets/42',
            new Response('{"datasets":[{"jrid": "42","foo":"bar","qux":"qoo"}]}', [], 200),
        );

        $actual = $this->subject->findByJrid(42);
        $expected = [[
            'jrid' => '42',
            'foo' => 'bar',
            'qux' => 'qoo',
        ]];

        self::assertSame($expected, $actual);

        $method = self::$server->getLastRequest()->getRequestMethod();
        self::assertSame('GET', $method);
    }

    /**
     * @test
     */
    public function findByJridThrowsExceptionWhenJridNotAvailable(): void
    {
        $this->expectException(DatasetNotAvailableException::class);
        $this->expectExceptionCode(1613047932);
        $this->expectExceptionMessage('Dataset with jrid "53" is not available');

        self::$server->setResponseOfPath(
            '/api/rest/v2/application/jobdata/tables/some-guid/datasets/53',
            new Response('{"errors":{"-": ["Record for given primary key not found."]}}', [], 404),
        );

        $this->subject->findByJrid(53);
    }
}
