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
use Brotkrueml\JobRouterData\EventListener\IntegerFormatter;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\ColumnBuilder;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\TestCase;

final class IntegerFormatterTest extends TestCase
{
    private IntegerFormatter $subject;

    protected function setUp(): void
    {
        $this->subject = new IntegerFormatter();
    }

    /**
     * @test
     * @dataProvider dataProviderForInvoke
     */
    public function invoke(FieldType $type, $content, string $locale, $expected): void
    {
        $table = (new TableBuilder())->build(1);
        $column = (new ColumnBuilder())->build(1, $type);

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
            'type' => FieldType::Integer,
            'content' => 123_456_789,
            'locale' => 'nl_BE.utf8',
            'expected' => '123.456.789',
        ];

        yield 'Column type is integer and content is null, content is not changed' => [
            'type' => FieldType::Integer,
            'content' => null,
            'locale' => 'nl_BE.utf8',
            'expected' => null,
        ];

        yield 'Column type is integer and content is a numeric string, content is formatted' => [
            'type' => FieldType::Integer,
            'content' => '123456789',
            'locale' => 'en_US',
            'expected' => '123,456,789',
        ];

        yield 'Column type is integer and content is a non-numeric string, content is not changed' => [
            'type' => FieldType::Integer,
            'content' => 'some content',
            'locale' => 'en_US',
            'expected' => 'some content',
        ];

        yield 'Column type is decimal, content is not changed' => [
            'type' => FieldType::Decimal,
            'content' => 123_456_789,
            'locale' => 'nl_BE.utf8',
            'expected' => 123_456_789,
        ];
    }
}
