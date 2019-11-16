<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\SyncCommand;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
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

    public function setUp(): void
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
            ->method('run');

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        $this->assertStringStartsWith('[OK]', trim($actual));
    }

    /**
     * @test
     */
    public function errorIsDisplayedWhenExceptionFromTableSynchroniser(): void
    {
        $this->synchronisationRunnerMock
            ->method('run')
            ->with(null)
            ->willThrowException(new SynchronisationException('some synchronisation error'));

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        $this->assertSame('[ERROR] some synchronisation error', trim($actual));
    }
}
