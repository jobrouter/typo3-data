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
use Brotkrueml\JobRouterConnector\Domain\Repository\ConnectionRepository;
use Brotkrueml\JobRouterConnector\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactoryInterface;
use Brotkrueml\JobRouterData\Controller\TableTestController;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\TableNotFoundException;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\ConnectionBuilder;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\StreamFactory;

final class TableTestControllerTest extends TestCase
{
    private ConnectionRepository&Stub $connectionRepositoryStub;
    private TableRepository&Stub $tableRepositoryStub;
    private ClientInterface&Stub $clientStub;
    private RestClientFactoryInterface&MockObject $restClientFactoryMock;
    private TableTestController $subject;
    private ServerRequestInterface&Stub $requestStub;

    protected function setUp(): void
    {
        $this->connectionRepositoryStub = $this->createStub(ConnectionRepository::class);
        $this->tableRepositoryStub = $this->createStub(TableRepository::class);
        $this->clientStub = $this->createStub(ClientInterface::class);
        $this->restClientFactoryMock = $this->createMock(RestClientFactoryInterface::class);

        $this->subject = new TableTestController(
            $this->connectionRepositoryStub,
            $this->tableRepositoryStub,
            $this->restClientFactoryMock,
            new ResponseFactory(),
            new StreamFactory(),
        );

        $this->requestStub = $this->createStub(ServerRequestInterface::class);
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenRequestHasInvalidBody(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn(null);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Request has no valid body!"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenTableIdentifierCannotBeFoundInRepository(): void
    {
        $this->tableRepositoryStub
            ->method('findByUidWithHidden')
            ->with(42)
            ->willThrowException(new TableNotFoundException('some error'));

        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Table with ID \"42\" not found!"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenConnectionIsNotAvailable(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $this->tableRepositoryStub
            ->method('findByUidWithHidden')
            ->with(42)
            ->willReturn((new TableBuilder())->build(42, connection: 21));

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(21, true)
            ->willThrowException(new ConnectionNotFoundException('some error'));

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Connection with ID \"21\" not found!"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnSuccessfulResponse(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $this->tableRepositoryStub
            ->method('findByUidWithHidden')
            ->with(42)
            ->willReturn((new TableBuilder())->build(42, connection: 21));

        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(21, true)
            ->willReturn((new ConnectionBuilder())->build(21));

        $actual = $this->subject->__invoke($this->requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"check": "ok"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenExceptionIsThrown(): void
    {
        $this->requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $this->tableRepositoryStub
            ->method('findByUidWithHidden')
            ->with(42)
            ->willReturn((new TableBuilder())->build(42, tableGuid: 'sometableguid', connection: 21));

        $connection = (new ConnectionBuilder())->build(21);
        $this->connectionRepositoryStub
            ->method('findByUid')
            ->with(21, true)
            ->willReturn($connection);

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
            $actual->getBody()->getContents(),
        );
    }
}
