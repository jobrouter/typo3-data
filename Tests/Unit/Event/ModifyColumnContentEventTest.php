<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Event;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Column;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Event\ModifyColumnContentEvent;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\ColumnBuilder;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ModifyColumnContentEventTest extends TestCase
{
    private Column $column;
    private Table $table;

    protected function setUp(): void
    {
        $this->table = (new TableBuilder())->build(1);
        $this->column = (new ColumnBuilder())->build(1);
    }

    #[Test]
    public function getTable(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', '');

        self::assertSame($this->table, $subject->getTable());
    }

    #[Test]
    public function getColumn(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', '');

        self::assertSame($this->column, $subject->getColumn());
    }

    #[Test]
    public function getContent(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, 'some content', '');

        self::assertSame('some content', $subject->getContent());
    }

    #[Test]
    public function setContent(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, 'some content', '');
        $subject->setContent('another content');

        self::assertSame('another content', $subject->getContent());
    }

    #[Test]
    public function getLocale(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', 'de');

        self::assertSame('de', $subject->getLocale());
    }

    #[Test]
    public function isPropagationStopped(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', '');

        self::assertFalse($subject->isPropagationStopped());

        $subject->setContent('some content');

        self::assertTrue($subject->isPropagationStopped());
    }
}
