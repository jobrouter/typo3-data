<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\EventListener;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;
use Brotkrueml\JobRouterData\EventListener\DateTimeFormatter;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\ColumnBuilder;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\TestCase;

final class DateTimeFormatterTest extends TestCase
{
    private DateTimeFormatter $subject;

    protected function setUp(): void
    {
        $this->subject = new DateTimeFormatter();
    }

    /**
     * @test
     */
    public function contentIsFormattedIfColumnTypeIsDate(): void
    {
        $table = (new TableBuilder())->build(1);
        $column = (new ColumnBuilder())->build(1, FieldType::DateTime);

        $event = new ModifyColumnContentEvent($table, $column, '2021-11-29T12:34:56+00:00', 'de');

        $this->subject->__invoke($event);

        self::assertSame('29.11.2021, 12:34', $event->getContent());
    }

    /**
     * @test
     */
    public function contentIsNotChangedIfColumnTypeIsNotDate(): void
    {
        $table = (new TableBuilder())->build(1);
        $column = (new ColumnBuilder())->build(1, FieldType::Date);

        $event = new ModifyColumnContentEvent($table, $column, '2021-11-29T12:34:56+00:00', 'de');

        $this->subject->__invoke($event);

        self::assertSame('2021-11-29T12:34:56+00:00', $event->getContent());
    }
}
