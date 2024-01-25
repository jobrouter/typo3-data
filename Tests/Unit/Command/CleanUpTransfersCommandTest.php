<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Command;

use JobRouter\AddOn\Typo3Data\Command\CleanUpTransfersCommand;
use JobRouter\AddOn\Typo3Data\Exception\DeleteException;
use JobRouter\AddOn\Typo3Data\Transfer\Deleter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CleanUpTransfersCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private Deleter&MockObject $deleterMock;

    protected function setUp(): void
    {
        $this->deleterMock = $this->createMock(Deleter::class);

        $this->commandTester = new CommandTester(new CleanUpTransfersCommand($this->deleterMock));
    }

    #[Test]
    public function okIsDisplayedWithNoTransfersForDeletionPresent(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(0);

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] No successful transfers older than 30 days present',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function okIsDisplayedWithOneTransferDeleted(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(1);

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 1 successful transfer older than 30 days deleted',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function okIsDisplayedWithMoreThanOneTransferDeleted(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(42);

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 42 successful transfers older than 30 days deleted',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function ageOfDaysIsRecognisedCorrectly(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(42);

        $this->commandTester->execute([
            'ageInDays' => 60,
        ]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 42 successful transfers older than 60 days deleted',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function errorIsDisplayedWhenAgeInDaysIsNotNumeric(): void
    {
        $this->deleterMock
            ->expects(self::never())
            ->method('run');

        $this->commandTester->execute([
            'ageInDays' => 'abc',
        ]);

        self::assertSame(Command::INVALID, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[ERROR] Argument "ageInDays" must be a number, "abc" given',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function errorIsDisplayedWhenAgeInDaysIsANegativeNumber(): void
    {
        $this->deleterMock
            ->expects(self::never())
            ->method('run');

        $this->commandTester->execute([
            'ageInDays' => '-42',
        ]);

        self::assertSame(Command::INVALID, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[ERROR] Argument "ageInDays" must not be a negative number, "-42" given',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function errorIsDisplayedWhenDeletionFails(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willThrowException(new DeleteException('some deletion error'));

        $this->commandTester->execute([]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[ERROR] some deletion error',
            $this->commandTester->getDisplay(),
        );
    }
}
