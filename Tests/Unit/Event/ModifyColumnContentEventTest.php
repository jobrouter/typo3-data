<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Event;

use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;
use PHPUnit\Framework\TestCase;

final class ModifyColumnContentEventTest extends TestCase
{
    /**
     * @var Column
     */
    private $column;

    /**
     * @var Table
     */
    private $table;

    protected function setUp(): void
    {
        $this->table = new Table();
        $this->column = new Column();
    }

    /**
     * @test
     */
    public function getTable(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', '');

        self::assertSame($this->table, $subject->getTable());
    }

    /**
     * @test
     */
    public function getColumn(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', '');

        self::assertSame($this->column, $subject->getColumn());
    }

    /**
     * @test
     */
    public function getContent(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, 'some content', '');

        self::assertSame('some content', $subject->getContent());
    }

    /**
     * @test
     */
    public function setContent(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, 'some content', '');
        $subject->setContent('another content');

        self::assertSame('another content', $subject->getContent());
    }

    /**
     * @test
     */
    public function getLocale(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', 'de');

        self::assertSame('de', $subject->getLocale());
    }

    /**
     * @test
     */
    public function isPropagationStopped(): void
    {
        $subject = new ModifyColumnContentEvent($this->table, $this->column, '', '');

        self::assertFalse($subject->isPropagationStopped());

        $subject->setContent('some content');

        self::assertTrue($subject->isPropagationStopped());
    }
}
