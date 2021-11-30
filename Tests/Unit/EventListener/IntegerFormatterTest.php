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
use Brotkrueml\JobRouterData\EventListener\IntegerFormatter;
use PHPUnit\Framework\TestCase;

final class IntegerFormatterTest extends TestCase
{
    /**
     * @var IntegerFormatter
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new IntegerFormatter();
    }

    /**
     * @test
     */
    public function contentIsFormattedIfColumnTypeIsInteger(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::INTEGER);

        $event = new ModifyColumnContentEvent($table, $column, 123456789, 'nl_BE.utf8');

        $this->subject->__invoke($event);

        self::assertSame('123.456.789', $event->getContent());
    }

    /**
     * @test
     */
    public function contentIsNotChangedIfColumnTypeIsNotInteger(): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType(FieldTypeEnumeration::DECIMAL);

        $event = new ModifyColumnContentEvent($table, $column, 123456789, 'nl_BE.utf8');

        $this->subject->__invoke($event);

        self::assertSame(123456789, $event->getContent());
    }
}
