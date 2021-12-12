<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterData\Domain\Model\Column;
use PHPUnit\Framework\TestCase;

final class ColumnTest extends TestCase
{
    /**
     * @var Column
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new Column();
    }

    /**
     * @test
     */
    public function getAndSetName(): void
    {
        self::assertSame('', $this->subject->getName());

        $this->subject->setName('some name');

        self::assertSame('some name', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getAndSetLabel(): void
    {
        self::assertSame('', $this->subject->getLabel());

        $this->subject->setLabel('some label');

        self::assertSame('some label', $this->subject->getLabel());
    }

    /**
     * @test
     */
    public function getAndSetType(): void
    {
        self::assertSame(0, $this->subject->getType());

        $this->subject->setType(42);

        self::assertSame(42, $this->subject->getType());
    }

    /**
     * @test
     */
    public function getAndSetDecimalPlaces(): void
    {
        self::assertSame(0, $this->subject->getDecimalPlaces());

        $this->subject->setDecimalPlaces(2);

        self::assertSame(2, $this->subject->getDecimalPlaces());
    }

    /**
     * @test
     */
    public function getAndSetFieldSize(): void
    {
        self::assertSame(0, $this->subject->getFieldSize());

        $this->subject->setFieldSize(50);

        self::assertSame(50, $this->subject->getFieldSize());
    }

    /**
     * @test
     */
    public function getAndSetAlignment(): void
    {
        self::assertSame('', $this->subject->getAlignment());

        $this->subject->setAlignment('center');

        self::assertSame('center', $this->subject->getAlignment());
    }

    /**
     * @test
     */
    public function getAndSetSortingPriority(): void
    {
        self::assertSame(0, $this->subject->getSortingPriority());

        $this->subject->setSortingPriority(42);

        self::assertSame(42, $this->subject->getSortingPriority());
    }

    /**
     * @test
     */
    public function getAndSetSortingOrder(): void
    {
        self::assertSame('', $this->subject->getSortingOrder());

        $this->subject->setSortingOrder('desc');

        self::assertSame('desc', $this->subject->getSortingOrder());
    }
}
