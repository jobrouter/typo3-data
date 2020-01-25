<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\SyncCommand;
use Brotkrueml\JobRouterData\Synchronisation\SynchronisationRunner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SyncCommandTest extends TestCase
{

    /** @var MockObject|SynchronisationRunner */
    private $synchronisationRunnerMock;

    /** @var CommandTester */
    private $commandTester;

    protected function setUp(): void
    {
        $this->synchronisationRunnerMock = $this->createMock(SynchronisationRunner::class);

        $command = new SyncCommand();
        $command->setSynchronisationRunner($this->synchronisationRunnerMock);

        $this->commandTester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function okIsDisplayedWhenSynchronisationIsSuccessful()
    {
        $this->synchronisationRunnerMock
            ->method('run')
            ->willReturn([2, 0]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertStringStartsWith('[OK]', trim($actual));
    }

    /**
     * @test
     */
    public function errorIsDisplayedWhenExceptionFromTableSynchroniser(): void
    {
        $this->synchronisationRunnerMock
            ->method('run')
            ->willReturn([3, 1]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[WARNING] 1 out of 3 table(s) had errors on synchronisation', trim($actual));
    }
}
