<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
    public function getAndSetCorrelationIdImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getCorrelationId());

        $this->subject->setCorrelationId('some correlation id');

        self::assertSame('some correlation id', $this->subject->getCorrelationId());
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
