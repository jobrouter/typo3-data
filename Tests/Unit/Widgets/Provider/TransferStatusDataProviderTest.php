<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Widgets\Provider;

use Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterData\Extension;
use Brotkrueml\JobRouterData\Widgets\Provider\TransferStatusDataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Registry;

class TransferStatusDataProviderTest extends TestCase
{
    /** @var Stub|Registry */
    private $registryStub;

    /** @var Stub|TransferRepository */
    private $transferRepositoryStub;

    /** @var TransferStatusDataProvider */
    private $subject;

    protected function setUp(): void
    {
        $this->registryStub = $this->createStub(Registry::class);
        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferStatusDataProvider($this->registryStub, $this->transferRepositoryStub);
    }

    /**
     * @test
     */
    public function getStatusReturnsNoCountsWhenNoTransfersAreAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([]);

        $this->registryStub
            ->method('get');

        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getSuccessfulCount());
        self::assertSame(0, $actual->getPendingCount());
        self::assertSame(0, $actual->getFailedCount());
    }

    /**
     * @test
     */
    public function getStatusReturnsSuccessfulCountsWhenOnlySuccessfulTransfersAreAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([
                [
                    'transmit_success' => 1,
                    'count' => 16,
                ]
            ]);

        $this->registryStub
            ->method('get');

        $actual = $this->subject->getStatus();

        self::assertSame(16, $actual->getSuccessfulCount());
        self::assertSame(0, $actual->getPendingCount());
        self::assertSame(0, $actual->getFailedCount());
    }

    /**
     * @test
     */
    public function getStatusReturnsPendingCountsWhenOnlyPendingTransfersAreAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([
                [
                    'transmit_success' => 0,
                    'count' => 8,
                ]
            ]);
        $this->transferRepositoryStub
            ->method('countTransmitFailed')
            ->willReturn(0);

        $this->registryStub
            ->method('get');

        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getSuccessfulCount());
        self::assertSame(8, $actual->getPendingCount());
        self::assertSame(0, $actual->getFailedCount());
    }

    /**
     * @test
     */
    public function getStatusReturnsFailedCountsWhenOnlyFailedTransfersAreAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([
                [
                    'transmit_success' => 0,
                    'count' => 4,
                ]
            ]);
        $this->transferRepositoryStub
            ->method('countTransmitFailed')
            ->willReturn(4);

        $this->registryStub
            ->method('get');

        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getSuccessfulCount());
        self::assertSame(0, $actual->getPendingCount());
        self::assertSame(4, $actual->getFailedCount());
    }

    /**
     * @test
     */
    public function getStatusReturnsCorrectCountsWhenAllStatusesAreAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([
                [
                    'transmit_success' => 1,
                    'count' => 42,
                ],
                [
                'transmit_success' => 0,
                'count' => 28,
            ]
            ]);
        $this->transferRepositoryStub
            ->method('countTransmitFailed')
            ->willReturn(12);

        $this->registryStub
            ->method('get');

        $actual = $this->subject->getStatus();

        self::assertSame(42, $actual->getSuccessfulCount());
        self::assertSame(16, $actual->getPendingCount());
        self::assertSame(12, $actual->getFailedCount());
    }

    /**
     * @test
     */
    public function getStatusReturnsNullForLastRunWhenNoRegistryEntryAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([]);

        $this->registryStub
            ->method('get');

        $actual = $this->subject->getStatus();

        self::assertNull($actual->getLastRun());
    }

    /**
     * @test
     */
    public function getStatusReturnsDateTimeForLastRunWhenRegistryEntryAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByTransmitSuccess')
            ->willReturn([]);

        $this->registryStub
            ->method('get')
            ->with(Extension::REGISTRY_NAMESPACE, 'transmitCommand.lastRun')
            ->willReturn([
                'start' => 1601889643,
                'end' => 1601889645,
                'exitCode' => 0,
            ]);

        $actual = $this->subject->getStatus();

        self::assertSame('1601889643', $actual->getLastRun()->format('U'));
    }
}
