<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Controller;

use JobRouter\AddOn\RestClient\Client\ClientInterface;
use JobRouter\AddOn\Typo3Connector\Domain\Repository\ConnectionRepository;
use JobRouter\AddOn\Typo3Connector\Exception\ConnectionNotFoundException;
use JobRouter\AddOn\Typo3Connector\RestClient\RestClientFactoryInterface;
use JobRouter\AddOn\Typo3Data\Controller\TableTestController;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\ConnectionBuilder;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\StreamFactory;

final class TableTestControllerTest extends TestCase
{
    #[Test]
    public function invokeReturnsResponseWithErrorWhenRequestHasInvalidBody(): void
    {
        $requestStub = self::createStub(ServerRequestInterface::class);
        $requestStub
            ->method('getParsedBody')
            ->willReturn(null);

        $subject = new TableTestController(
            self::createStub(ConnectionRepository::class),
            self::createStub(TableRepository::class),
            self::createStub(RestClientFactoryInterface::class),
            new ResponseFactory(),
            new StreamFactory(),
        );

        $actual = $subject->__invoke($requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Request has no valid body!"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenTableIdentifierCannotBeFoundInRepository(): void
    {
        $tableRepositoryMock = self::createMock(TableRepository::class);
        $tableRepositoryMock
            ->expects(self::once())
            ->method('findByUidWithHidden')
            ->with(42)
            ->willThrowException(new TableNotFoundException('some error'));

        $subject = new TableTestController(
            self::createStub(ConnectionRepository::class),
            $tableRepositoryMock,
            self::createStub(RestClientFactoryInterface::class),
            new ResponseFactory(),
            new StreamFactory(),
        );

        $requestStub = self::createStub(ServerRequestInterface::class);
        $requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $actual = $subject->__invoke($requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Table with ID \"42\" not found!"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenConnectionIsNotAvailable(): void
    {
        $connectionRepositoryMock = self::createMock(ConnectionRepository::class);
        $connectionRepositoryMock
            ->expects(self::once())
            ->method('findByUid')
            ->with(21, true)
            ->willThrowException(new ConnectionNotFoundException('some error'));

        $tableRepositoryMock = self::createMock(TableRepository::class);
        $tableRepositoryMock
            ->expects(self::once())
            ->method('findByUidWithHidden')
            ->with(42)
            ->willReturn((new TableBuilder())->build(42, connection: 21));

        $subject = new TableTestController(
            $connectionRepositoryMock,
            $tableRepositoryMock,
            self::createStub(RestClientFactoryInterface::class),
            new ResponseFactory(),
            new StreamFactory(),
        );

        $requestStub = self::createStub(ServerRequestInterface::class);
        $requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $actual = $subject->__invoke($requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error": "Connection with ID \"21\" not found!"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnSuccessfulResponse(): void
    {
        $connectionRepositoryMock = self::createMock(ConnectionRepository::class);
        $connectionRepositoryMock
            ->expects(self::once())
            ->method('findByUid')
            ->with(21, true)
            ->willReturn((new ConnectionBuilder())->build(21));

        $tableRepositoryMock = self::createMock(TableRepository::class);
        $tableRepositoryMock
            ->expects(self::once())
            ->method('findByUidWithHidden')
            ->with(42)
            ->willReturn((new TableBuilder())->build(42, connection: 21));

        $subject = new TableTestController(
            $connectionRepositoryMock,
            $tableRepositoryMock,
            self::createStub(RestClientFactoryInterface::class),
            new ResponseFactory(),
            new StreamFactory(),
        );

        $requestStub = self::createStub(ServerRequestInterface::class);
        $requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $actual = $subject->__invoke($requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"check": "ok"}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function invokeReturnsResponseWithErrorWhenExceptionIsThrown(): void
    {
        $connection = (new ConnectionBuilder())->build(21);
        $connectionRepositoryMock = self::createMock(ConnectionRepository::class);
        $connectionRepositoryMock
            ->expects(self::once())
            ->method('findByUid')
            ->with(21, true)
            ->willReturn($connection);

        $tableRepositoryMock = self::createMock(TableRepository::class);
        $tableRepositoryMock
            ->expects(self::once())
            ->method('findByUidWithHidden')
            ->with(42)
            ->willReturn((new TableBuilder())->build(42, tableGuid: 'sometableguid', connection: 21));

        $clientMock = self::createMock(ClientInterface::class);
        $clientMock
            ->expects(self::once())
            ->method('request')
            ->with('GET', 'application/jobdata/tables/sometableguid/datasets')
            ->willThrowException(new \Exception('some exception message'));
        $restClientFactoryMock = self::createMock(RestClientFactoryInterface::class);
        $restClientFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with($connection)
            ->willReturn($clientMock);

        $subject = new TableTestController(
            $connectionRepositoryMock,
            $tableRepositoryMock,
            $restClientFactoryMock,
            new ResponseFactory(),
            new StreamFactory(),
        );

        $requestStub = self::createStub(ServerRequestInterface::class);
        $requestStub
            ->method('getParsedBody')
            ->willReturn([
                'tableId' => '42',
            ]);

        $actual = $subject->__invoke($requestStub);
        $actual->getBody()->rewind();

        self::assertJsonStringEqualsJsonString(
            '{"error":"some exception message"}',
            $actual->getBody()->getContents(),
        );
    }
}
