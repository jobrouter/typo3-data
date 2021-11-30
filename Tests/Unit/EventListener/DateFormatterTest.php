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
use Brotkrueml\JobRouterData\EventListener\DateFormatter;
use PHPUnit\Framework\TestCase;

final class DateFormatterTest extends TestCase
{
    /**
     * @var DateFormatter
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new DateFormatter();
    }

    /**
     * @test
     */
    public function contentIsFormattedIfColumnTypeIsDate(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::DATE);

        $event = new ModifyColumnContentEvent($table, $column, '2021-11-29T00:00:00+00:00', 'en_GB');

        $this->subject->__invoke($event);

        self::assertSame('29 Nov 2021', $event->getContent());
    }

    /**
     * @test
     */
    public function contentIsNotChangedIfColumnTypeIsNotDate(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::DATETIME);

        $event = new ModifyColumnContentEvent($table, $column, '2021-11-29T00:00:00+00:00', 'en_GB');

        $this->subject->__invoke($event);

        self::assertSame('2021-11-29T00:00:00+00:00', $event->getContent());
    }
}
