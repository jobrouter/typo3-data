<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\TransmitCommand;
use Brotkrueml\JobRouterData\Domain\Dto\CountResult;
use Brotkrueml\JobRouterData\Transfer\Transmitter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;

final class TransmitCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private LockingStrategyInterface&MockObject $lockerMock;
    private Transmitter&MockObject $transmitterMock;
    private Registry&MockObject $registryMock;

    protected function setUp(): void
    {
        $this->lockerMock = $this->createMock(LockingStrategyInterface::class);
        $lockFactoryStub = $this->createStub(LockFactory::class);
        $lockFactoryStub
            ->method('createLocker')
            ->willReturn($this->lockerMock);

        $this->transmitterMock = $this->createMock(Transmitter::class);
        $this->registryMock = $this->createMock(Registry::class);

        $command = new TransmitCommand($lockFactoryStub, $this->registryMock, $this->transmitterMock);
        $this->commandTester = new CommandTester($command);
    }

    #[Test]
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
            ->willReturn(new CountResult(0, 0));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'transmitCommand.lastRun',
                self::callback(
                    static fn($subject): bool => $subject['exitCode'] === Command::SUCCESS,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 0 transfer(s) transmitted successfully',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
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
            ->willReturn(new CountResult(3, 0));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'transmitCommand.lastRun',
                self::callback(
                    static fn($subject): bool => $subject['exitCode'] === Command::SUCCESS,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 3 transfer(s) transmitted successfully',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
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
            ->willReturn(new CountResult(3, 1));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                'tx_jobrouter_data',
                'transmitCommand.lastRun',
                self::callback(
                    static fn($subject): bool => $subject['exitCode'] === Command::FAILURE,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] 1 out of 3 transfer(s) had errors on transmission',
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

        $this->transmitterMock
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
