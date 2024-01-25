<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Command;

use JobRouter\AddOn\Typo3Data\Command\SyncCommand;
use JobRouter\AddOn\Typo3Data\Domain\Dto\CountResult;
use JobRouter\AddOn\Typo3Data\Synchronisation\SynchronisationRunner;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;

final class SyncCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private LockingStrategyInterface&MockObject $lockerMock;
    private SynchronisationRunner&MockObject $synchronisationRunnerMock;
    private Registry&MockObject $registryMock;

    protected function setUp(): void
    {
        $this->lockerMock = $this->createMock(LockingStrategyInterface::class);
        $lockFactoryStub = $this->createStub(LockFactory::class);
        $lockFactoryStub
            ->method('createLocker')
            ->willReturn($this->lockerMock);

        $this->registryMock = $this->createMock(Registry::class);
        $this->synchronisationRunnerMock = $this->createMock(SynchronisationRunner::class);

        $command = new SyncCommand($lockFactoryStub, $this->registryMock, $this->synchronisationRunnerMock);
        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function okIsDisplayedWhenAllSynchronisationsAreSuccessful(): void
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
            ->willReturn(new CountResult(2, 0));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'syncCommand.lastRun',
                self::callback(
                    static fn($subject): bool => $subject['exitCode'] === Command::SUCCESS,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString('2 table(s) processed', $this->commandTester->getDisplay());
    }

    #[Test]
    public function okIsDisplayedWhenSynchronisationForOneTableIsSuccessful(): void
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
            ->willReturn(new CountResult(1, 0));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'syncCommand.lastRun',
                self::callback(
                    static fn($subject): bool => $subject['exitCode'] === Command::SUCCESS,
                ),
            );

        $this->commandTester->execute([
            'table' => 'some_handle',
        ]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'Table with handle "some_handle" processed',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
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
            ->willReturn(new CountResult(3, 1));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'syncCommand.lastRun',
                self::callback(
                    static fn($subject): bool => $subject['exitCode'] === Command::FAILURE,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] 1 out of 3 table(s) had errors during processing',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
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

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '! [NOTE] Could not acquire lock, another process is running',
            $this->commandTester->getDisplay(),
        );
    }
}
