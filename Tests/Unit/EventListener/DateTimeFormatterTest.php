<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\EventListener;

use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;
use JobRouter\AddOn\Typo3Data\Event\ModifyColumnContentEvent;
use JobRouter\AddOn\Typo3Data\EventListener\DateTimeFormatter;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\ColumnBuilder;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTimeFormatterTest extends TestCase
{
    private DateTimeFormatter $subject;

    protected function setUp(): void
    {
        $this->subject = new DateTimeFormatter();
    }

    #[Test]
    public function contentIsFormattedIfColumnTypeIsDate(): void
    {
        $table = (new TableBuilder())->build(1);
        $column = (new ColumnBuilder())->build(1, FieldType::DateTime);

        $event = new ModifyColumnContentEvent($table, $column, '2021-11-29T12:34:56+00:00', 'de');

        $this->subject->__invoke($event);

        self::assertSame('29.11.2021, 12:34', $event->getContent());
    }

    #[Test]
    public function contentIsNotChangedIfColumnTypeIsNotDate(): void
    {
        $table = (new TableBuilder())->build(1);
        $column = (new ColumnBuilder())->build(1, FieldType::Date);

        $event = new ModifyColumnContentEvent($table, $column, '2021-11-29T12:34:56+00:00', 'de');

        $this->subject->__invoke($event);

        self::assertSame('2021-11-29T12:34:56+00:00', $event->getContent());
    }
}
