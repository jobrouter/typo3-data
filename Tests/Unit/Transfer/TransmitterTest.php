<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Transfer;

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Transfer\Transmitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class TransmitterTest extends TestCase
{
    private Transmitter $subject;

    /**
     * @var MockObject&PersistenceManagerInterface
     */
    private MockObject $persistenceManagerMock;

    /**
     * @var MockObject&TransferRepository
     */
    private MockObject $transferRepositoryMock;

    /**
     * @var MockObject&TableRepository
     */
    private MockObject $tableRepositoryMock;

    protected function setUp(): void
    {
        $this->persistenceManagerMock = $this->createMock(PersistenceManagerInterface::class);

        $restClientStub = $this->createStub(RestClientFactory::class);

        $this->transferRepositoryMock = $this->getMockBuilder(TransferRepository::class)
            ->disableOriginalConstructor()
            ->addMethods(['findByTransmitSuccess'])
            ->onlyMethods(['update'])
            ->getMock();

        $this->tableRepositoryMock = $this->getMockBuilder(TableRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new Transmitter(
            new NullLogger(),
            $this->persistenceManagerMock,
            $restClientStub,
            $this->transferRepositoryMock,
            $this->tableRepositoryMock
        );
    }

    /**
     * @test
     */
    public function transmitWithNoTransfersAvailableReturns0TotalsAndErrors(): void
    {
        $this->transferRepositoryMock
            ->method('findByTransmitSuccess')
            ->willReturn([]);

        $actual = $this->subject->run();

        self::assertSame(0, $actual->total);
        self::assertSame(0, $actual->errors);
    }
}
