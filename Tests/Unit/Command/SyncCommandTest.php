<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\SyncCommand;
use Brotkrueml\JobRouterData\Synchronisation\SynchronisationRunner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SyncCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var MockObject|LockingStrategyInterface */
    private $lockerMock;

    /** @var MockObject|SynchronisationRunner */
    private $synchronisationRunnerMock;

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

        $this->registryMock = $this->createMock(Registry::class);
        GeneralUtility::setSingletonInstance(Registry::class, $this->registryMock);

        $this->synchronisationRunnerMock = $this->createMock(SynchronisationRunner::class);

        $command = new SyncCommand();
        $command->setSynchronisationRunner($this->synchronisationRunnerMock);

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     */
    public function okIsDisplayedWhenAllSynchronisationsAreSuccessful()
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->synchronisationRunnerMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([2, 0]);

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'syncCommand.lastRun',
                self::callback(
                    function ($subject) {
                        return $subject['exitCode'] === SyncCommand::EXIT_CODE_OK;
                    }
                )
            );

        $this->commandTester->execute([]);

        self::assertSame(SyncCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString('2 table(s) synchronised successfully', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function okIsDisplayedWhenSynchronisationForOneTableIsSuccessful()
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->synchronisationRunnerMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([1, 0]);

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'syncCommand.lastRun',
                self::callback(
                    function ($subject) {
                        return $subject['exitCode'] === SyncCommand::EXIT_CODE_OK;
                    }
                )
            );

        $this->commandTester->execute(['table' => 42]);

        self::assertSame(SyncCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'Table with uid "42" synchronised successfully',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWhenExceptionFromTableSynchroniser(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->synchronisationRunnerMock
            ->method('run')
            ->willReturn([3, 1]);

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'syncCommand.lastRun',
                self::callback(
                    function ($subject) {
                        return $subject['exitCode'] === SyncCommand::EXIT_CODE_ERRORS_ON_SYNCHRONISATION;
                    }
                )
            );

        $this->commandTester->execute([]);

        self::assertSame(SyncCommand::EXIT_CODE_ERRORS_ON_SYNCHRONISATION, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] 1 out of 3 table(s) had errors on synchronisation',
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

        $this->synchronisationRunnerMock
            ->expects(self::never())
            ->method('run');

        $this->registryMock
            ->expects(self::never())
            ->method('set');

        $this->commandTester->execute([]);

        self::assertSame(SyncCommand::EXIT_CODE_CANNOT_ACQUIRE_LOCK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] Could not acquire lock, another process is running',
            $this->commandTester->getDisplay()
        );
    }
}
