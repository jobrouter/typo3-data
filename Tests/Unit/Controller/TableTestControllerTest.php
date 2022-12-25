<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Controller;

use Brotkrueml\JobRouterClient\Client\ClientInterface;
use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactoryInterface;
use Brotkrueml\JobRouterData\Controller\TableTestController;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\StreamFactory;

final class TableTestControllerTest extends TestCase
{
    private TableRepository&Stub $tableRepositoryStub;
    private ClientInterface&Stub $clientStub;
    private RestClientFactoryInterface&MockObject $restClientFactoryMock;
    private TableTestController $subject;
    private ServerRequestInterface&Stub $requestStub;

    /**
     * @test
     */
    public function invokeReturnsResponseWithErrorWhenRequestHasInvalidBody(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn(null);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Request has no valid body!"}',
            $actual->getBody()->getContents()
        );
    }

    /**
     * @test
     */
    public function invokeReturnsResponseWithErrorWhenIdentifierCannotBeFoundInRepository(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Table with id \"42\" not found!"}',
            $actual->getBody()->getContents()
        );
    }

    /**
     * @test
     */
    public function invokeReturnsResponseWithErrorWhenConnectionIsNotAvailable(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $table = new Table();
        $this->tableRepositoryStub
            ->method('findByIdentifierWithHidden')
            ->with(42)
            ->willReturn($table);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Connection with id \"42\" not found or disabled!"}',
            $actual->getBody()->getContents()
        );
    }

    /**
     * @test
     */
    public function invokeReturnSuccessfulResponse(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $connection = new Connection();
        $table = new Table();
        $table->setConnection($connection);
        $table->setTableGuid('sometableguid');
        $this->tableRepositoryStub
            ->method('findByIdentifierWithHidden')
            ->with(42)
            ->willReturn($table);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"check": "ok"}',
            $actual->getBody()->getContents()
        );
    }

    /**
     * @test
     */
    public function invokeReturnsResponseWithErrorWhenExceptionIsThrown(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $connection = new Connection();
        $table = new Table();
        $table->setConnection($connection);
        $table->setTableGuid('sometableguid');
        $this->tableRepositoryStub
            ->method('findByIdentifierWithHidden')
            ->with(42)
            ->willReturn($table);

        $this->clientStub
            ->method('request')
            ->with('HEAD', 'application/jobdata/tables/sometableguid/datasets')
            ->willThrowException(new \Exception('some exception message'));
        $this->restClientFactoryMock
            ->method('create')
            ->with($connection)
            ->willReturn($this->clientStub);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error":"some exception message"}',
            $actual->getBody()->getContents()
        );
    }

    protected function setUp(): void
    {
        $this->tableRepositoryStub = $this->createStub(TableRepository::class);
        $this->clientStub = $this->createStub(ClientInterface::class);
        $this->restClientFactoryMock = $this->createMock(RestClientFactoryInterface::class);

        $this->subject = new TableTestController(
            $this->tableRepositoryStub,
            $this->restClientFactoryMock,
            new ResponseFactory(),
            new StreamFactory()
        );

        $this->requestStub = $this->createStub(ServerRequestInterface::class);
    }
}
