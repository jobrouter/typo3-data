<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\EventListener;

use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;
use Brotkrueml\JobRouterData\EventListener\DecimalFormatter;
use PHPUnit\Framework\TestCase;

final class DecimalFormatterTest extends TestCase
{
    /**
     * @var DecimalFormatter
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new DecimalFormatter();
    }

    /**
     * @test
     */
    public function contentIsFormattedIfColumnTypeIsDecimalAndFractionIsCut(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::DECIMAL);
        $column->setDecimalPlaces(3);

        $event = new ModifyColumnContentEvent($table, $column, 123456789.12345, 'de_CH');

        $this->subject->__invoke($event);

        self::assertSame('123’456’789.123', $event->getContent());
    }

    /**
     * @test
     */
    public function contentIsFormattedIfColumnTypeIsDecimalAndFractionIsPadded(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::DECIMAL);
        $column->setDecimalPlaces(4);

        $event = new ModifyColumnContentEvent($table, $column, 123456789.1, 'de_CH');

        $this->subject->__invoke($event);

        self::assertSame('123’456’789.1000', $event->getContent());
    }

    /**
     * @test
     */
    public function contentIsNotChangedIfColumnTypeIsNotInteger(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::INTEGER);

        $event = new ModifyColumnContentEvent($table, $column, 123456789, 'de_CH');

        $this->subject->__invoke($event);

        self::assertSame(123456789, $event->getContent());
    }
}
