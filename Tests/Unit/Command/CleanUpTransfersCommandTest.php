<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\CleanUpTransfersCommand;
use Brotkrueml\JobRouterData\Exception\DeleteException;
use Brotkrueml\JobRouterData\Transfer\Deleter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CleanUpTransfersCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var Deleter|MockObject */
    private $deleterMock;

    protected function setUp(): void
    {
        $this->deleterMock = $this->createMock(Deleter::class);

        $this->commandTester = new CommandTester(new CleanUpTransfersCommand($this->deleterMock));
    }

    /**
     * @test
     */
    public function okIsDisplayedWithNoTransfersForDeletionPresent(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(0);

        $this->commandTester->execute([]);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] No successful transfers older than 30 days present',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function okIsDisplayedWithOneTransferDeleted(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(1);

        $this->commandTester->execute([]);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 1 successful transfer older than 30 days deleted',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function okIsDisplayedWithMoreThanOneTransferDeleted(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(42);

        $this->commandTester->execute([]);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 42 successful transfers older than 30 days deleted',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function ageOfDaysIsRecognisedCorrectly(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(42);

        $this->commandTester->execute(['ageInDays' => 60]);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 42 successful transfers older than 60 days deleted',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function errorIsDisplayedWhenAgeInDaysIsNotNumeric(): void
    {
        $this->deleterMock
            ->expects(self::never())
            ->method('run');

        $this->commandTester->execute(['ageInDays' => 'abc']);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_INVALID_ARGUMENT, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[ERROR] Argument "ageInDays" must be a number, "abc" given',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function errorIsDisplayedWhenAgeInDaysIsANegativeNumber(): void
    {
        $this->deleterMock
            ->expects(self::never())
            ->method('run');

        $this->commandTester->execute(['ageInDays' => '-42']);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_INVALID_ARGUMENT, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[ERROR] Argument "ageInDays" must not be a negative number, "-42" given',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function errorIsDisplayedWhenDeletionFails(): void
    {
        $this->deleterMock
            ->expects(self::once())
            ->method('run')
            ->willThrowException(new DeleteException('some deletion error'));

        $this->commandTester->execute([]);

        self::assertSame(CleanUpTransfersCommand::EXIT_CODE_DELETION_FAILED, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[ERROR] some deletion error',
            $this->commandTester->getDisplay()
        );
    }
}
