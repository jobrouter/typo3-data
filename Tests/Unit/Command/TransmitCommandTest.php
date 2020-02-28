<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\SyncCommand;
use Brotkrueml\JobRouterData\Command\TransmitCommand;
use Brotkrueml\JobRouterData\Transfer\Transmitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TransmitCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var LockingStrategyInterface|MockObject */
    private $lockerMock;

    /** @var Transmitter|MockObject */
    private $transmitterMock;

    /** @var MockObject|Registry */
    private $registryMock;

    protected function setUp(): void
    {
        $this->lockerMock = $this->createMock(LockingStrategyInterface::class);

        $lockFactoryStub = $this->createStub(LockFactory::class);
        $lockFactoryStub
            ->method('createLocker')
            ->willReturn($this->lockerMock);

        GeneralUtility::setSingletonInstance(LockFactory::class, $lockFactoryStub);

        $this->transmitterMock = $this->createMock(Transmitter::class);
        GeneralUtility::addInstance(Transmitter::class, $this->transmitterMock);

        $this->registryMock = $this->createMock(Registry::class);
        GeneralUtility::setSingletonInstance(Registry::class, $this->registryMock);

        $this->commandTester = new CommandTester(new TransmitCommand());
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     */
    public function okIsDisplayedWithNoTransfersAvailable(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->transmitterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([0, 0]);

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'transmitCommand.lastRun',
                self::callback(
                    function ($subject) {
                        return $subject['exitCode'] === SyncCommand::EXIT_CODE_OK;
                    }
                )
            );

        $this->commandTester->execute([]);

        self::assertSame(TransmitCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 0 transfer(s) transmitted successfully',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function okIsDisplayedWithTransfersAvailableAndNoErrors(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->transmitterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([3, 0]);

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'transmitCommand.lastRun',
                self::callback(
                    function ($subject) {
                        return $subject['exitCode'] === SyncCommand::EXIT_CODE_OK;
                    }
                )
            );

        $this->commandTester->execute([]);

        self::assertSame(TransmitCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 3 transfer(s) transmitted successfully',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWithErrorsOccured(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->transmitterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([3, 1]);

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'transmitCommand.lastRun',
                self::callback(
                    function ($subject) {
                        return $subject['exitCode'] === SyncCommand::EXIT_CODE_ERRORS_ON_SYNCHRONISATION;
                    }
                )
            );

        $this->commandTester->execute([]);

        self::assertSame(TransmitCommand::EXIT_CODE_ERRORS_ON_TRANSMISSION, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] 1 out of 3 transfer(s) had errors on transmission',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWhenLockCannotBeAcquired(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willThrowException(new LockAcquireException());

        $this->lockerMock
            ->expects(self::never())
            ->method('release');

        $this->transmitterMock
            ->expects(self::never())
            ->method('run');

        $this->registryMock
            ->expects(self::never())
            ->method('set');

        $this->commandTester->execute([]);

        self::assertSame(TransmitCommand::EXIT_CODE_CANNOT_ACQUIRE_LOCK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] Could not acquire lock, another process is running',
            $this->commandTester->getDisplay()
        );
    }
}
