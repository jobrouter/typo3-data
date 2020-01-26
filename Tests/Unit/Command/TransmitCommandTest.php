<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Command;

use Brotkrueml\JobRouterData\Command\TransmitCommand;
use Brotkrueml\JobRouterData\Transfer\Transmitter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TransmitCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var Transmitter|Stub */
    private $transmitterStub;

    protected function setUp(): void
    {
        $this->transmitterStub = $this->createStub(Transmitter::class);
        GeneralUtility::addInstance(Transmitter::class, $this->transmitterStub);

        $this->commandTester = new CommandTester(new TransmitCommand());
    }

    /**
     * @test
     */
    public function okIsDisplayedWithNoTransfersAvailable(): void
    {
        $this->transmitterStub
            ->method('run')
            ->willReturn([0, 0]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[OK] 0 transfer(s) transmitted successfully', \trim($actual));
    }

    /**
     * @test
     */
    public function okIsDisplayedWithTransfersAvailableAndNoErrors(): void
    {
        $this->transmitterStub
            ->method('run')
            ->willReturn([3, 0]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[OK] 3 transfer(s) transmitted successfully', \trim($actual));
    }

    /**
     * @test
     */
    public function warningIsDisplayedWithErrorsOccured(): void
    {
        $this->transmitterStub
            ->method('run')
            ->willReturn([3, 1]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[WARNING] 1 out of 3 transfer(s) had errors on transmission', \trim($actual));
    }
}
