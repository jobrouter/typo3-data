<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\SyncCommand;
use Brotkrueml\JobRouterData\Exception\SynchronisationException;
use Brotkrueml\JobRouterData\Synchroniser\TableSynchroniser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SyncCommandTest extends TestCase
{

    /** @var MockObject|TableSynchroniser */
    private $tableSynchroniserMock;

    /** @var CommandTester */
    private $commandTester;

    public function setUp(): void
    {
        $this->tableSynchroniserMock = $this->createMock(TableSynchroniser::class);

        $command = new SyncCommand();
        $command->setTableSynchroniser($this->tableSynchroniserMock);

        $this->commandTester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function okIsDisplayedWhenSynchronisationIsSuccessful()
    {
        $this->tableSynchroniserMock
            ->method('synchronise');

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        $this->assertStringStartsWith('[OK]', trim($actual));
    }

    /**
     * @test
     */
    public function errorIsDisplayedWhenExceptionFromTableSynchroniser(): void
    {
        $this->tableSynchroniserMock
            ->method('synchronise')
            ->with(null)
            ->willThrowException(new SynchronisationException('some synchronisation error'));

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        $this->assertSame('[ERROR] some synchronisation error', trim($actual));
    }
}
