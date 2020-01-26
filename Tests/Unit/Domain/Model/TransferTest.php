<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use PHPUnit\Framework\TestCase;

class TransferTest extends TestCase
{
    /** @var Transfer */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new Transfer();
    }

    /**
     * @test
     */
    public function getAndSetTableUidImplementedCorrectly(): void
    {
        self::assertSame(0, $this->subject->getTableUid());

        $this->subject->setTableUid(42);

        self::assertSame(42, $this->subject->getTableUid());
    }

    /**
     * @test
     */
    public function getAndSetIdentifierImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getIdentifier());

        $this->subject->setIdentifier('some identifier');

        self::assertSame('some identifier', $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function getAndSetDataImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getData());

        $this->subject->setData('some data');

        self::assertSame('some data', $this->subject->getData());
    }

    /**
     * @test
     */
    public function isAndSetTransmitSuccessImplementedCorrectly(): void
    {
        self::assertFalse($this->subject->isTransmitSuccess());

        $this->subject->setTransmitSuccess(true);

        self::assertTrue($this->subject->isTransmitSuccess());
    }

    /**
     * @test
     */
    public function getAndSetTransmitDateImplementedCorrectly(): void
    {
        self::assertNull($this->subject->getTransmitDate());

        $date = new \DateTime();
        $this->subject->setTransmitDate($date);

        self::assertSame($date, $this->subject->getTransmitDate());
    }

    /**
     * @test
     */
    public function getAndSetTransmitMessageImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getTransmitMessage());

        $this->subject->setTransmitMessage('some transmit message');

        self::assertSame('some transmit message', $this->subject->getTransmitMessage());
    }
}
