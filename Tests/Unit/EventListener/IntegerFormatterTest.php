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
use JobRouter\AddOn\Typo3Data\EventListener\IntegerFormatter;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\ColumnBuilder;
use JobRouter\AddOn\Typo3Data\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IntegerFormatterTest extends TestCase
{
    private IntegerFormatter $subject;

    protected function setUp(): void
    {
        $this->subject = new IntegerFormatter();
    }

    #[Test]
    #[DataProvider('dataProviderForInvoke')]
    public function invoke(FieldType $type, int|null|string $content, string $locale, string|null|int $expected): void
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
    public static function dataProviderForInvoke(): iterable
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
