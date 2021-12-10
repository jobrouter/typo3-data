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
     * @dataProvider dataProviderForInvoke
     */
    public function invoke(int $type, $content, string $locale, $expected): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType($type);

        $event = new ModifyColumnContentEvent($table, $column, $content, $locale);
        $this->subject->__invoke($event);

        self::assertSame($expected, $event->getContent());
    }

    /**
     * @return \Iterator<array<string, float|int|string|null>
     */
    public function dataProviderForInvoke(): iterable
    {
        yield 'Column type is integer, content is formatted' => [
            'type' => FieldTypeEnumeration::INTEGER,
            'content' => 123456789,
            'locale' => 'nl_BE.utf8',
            'expected' => '123.456.789',
        ];

        yield 'Column type is integer and content is null, content is not changed' => [
            'type' => FieldTypeEnumeration::INTEGER,
            'content' => null,
            'locale' => 'nl_BE.utf8',
            'expected' => null,
        ];

        yield 'Column type is integer and content is a numeric string, content is formatted' => [
            'type' => FieldTypeEnumeration::INTEGER,
            'content' => '123456789',
            'locale' => 'en_US',
            'expected' => '123,456,789',
        ];

        yield 'Column type is integer and content is a non-numeric string, content is not changed' => [
            'type' => FieldTypeEnumeration::INTEGER,
            'content' => 'some content',
            'locale' => 'en_US',
            'expected' => 'some content',
        ];

        yield 'Column type is decimal, content is not changed' => [
            'type' => FieldTypeEnumeration::DECIMAL,
            'content' => 123456789,
            'locale' => 'nl_BE.utf8',
            'expected' => 123456789,
        ];
    }
}
