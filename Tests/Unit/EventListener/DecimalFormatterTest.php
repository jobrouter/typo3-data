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
use Brotkrueml\JobRouterData\EventListener\DecimalFormatter;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\ColumnBuilder;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\TestCase;

final class DecimalFormatterTest extends TestCase
{
    private DecimalFormatter $subject;

    protected function setUp(): void
    {
        $this->subject = new DecimalFormatter();
    }

    /**
     * @test
     * @dataProvider dataProviderForInvoke
     */
    public function invoke(FieldType $type, int $decimalPlaces, float|null|string|int $content, string $locale, string|null|int $expected): void
    {
        $table = (new TableBuilder())->build(1);
        $column = (new ColumnBuilder())->build(1, $type, $decimalPlaces);

        $event = new ModifyColumnContentEvent($table, $column, $content, $locale);
        $this->subject->__invoke($event);

        self::assertSame($expected, $event->getContent());
    }

    /**
     * @return \Iterator<array<string, float|int|string|null>
     */
    public function dataProviderForInvoke(): iterable
    {
        yield 'Column type is decimal and fraction is cut' => [
            'type' => FieldType::Decimal,
            'decimalPlaces' => 3,
            'content' => 123_456_789.12345,
            'locale' => 'de_CH',
            'expected' => '123’456’789.123',
        ];

        yield 'Column type is decimal and fraction is padded' => [
            'type' => FieldType::Decimal,
            'decimalPlaces' => 4,
            'content' => 123_456_789.1,
            'locale' => 'de_CH',
            'expected' => '123’456’789.1000',
        ];

        yield 'Column type is decimal and content is null, content is not changed' => [
            'type' => FieldType::Decimal,
            'decimalPlaces' => 2,
            'content' => null,
            'locale' => 'de_CH',
            'expected' => null,
        ];

        yield 'Column type is decimal and content is a numeric string, content is formatted' => [
            'type' => FieldType::Decimal,
            'decimalPlaces' => 2,
            'content' => '123456.789',
            'locale' => 'de_DE',
            'expected' => '123.456,79',
        ];

        yield 'Column type is decimal and content is a non-numeric string, content is not changed' => [
            'type' => FieldType::Decimal,
            'decimalPlaces' => 2,
            'content' => 'some content',
            'locale' => 'de_DE',
            'expected' => 'some content',
        ];

        yield 'Column type is integer, content is not changed' => [
            'type' => FieldType::Integer,
            'decimalPlaces' => 2,
            'content' => 123_456_789,
            'locale' => 'de_CH',
            'expected' => 123_456_789,
        ];
    }
}
