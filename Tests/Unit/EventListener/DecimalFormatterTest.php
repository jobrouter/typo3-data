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
use Brotkrueml\JobRouterData\EventListener\DecimalFormatter;
use PHPUnit\Framework\TestCase;

final class DecimalFormatterTest extends TestCase
{
    /**
     * @var DecimalFormatter
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new DecimalFormatter();
    }

    /**
     * @test
     * @dataProvider dataProviderForInvoke
     */
    public function invoke(int $type, int $decimalPlaces, $content, string $locale, $expected): void
    {
        $table = new Table();
        $column = new Column();
        $column->setType($type);
        $column->setDecimalPlaces($decimalPlaces);

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
            'type' => FieldTypeEnumeration::DECIMAL,
            'decimalPlaces' => 3,
            'content' => 123456789.12345,
            'locale' => 'de_CH',
            'expected' => '123’456’789.123',
        ];

        yield 'Column type is decimal and fraction is padded' => [
            'type' => FieldTypeEnumeration::DECIMAL,
            'decimalPlaces' => 4,
            'content' => 123456789.1,
            'locale' => 'de_CH',
            'expected' => '123’456’789.1000',
        ];

        yield 'Column type is decimal and content is null, content is not changed' => [
            'type' => FieldTypeEnumeration::DECIMAL,
            'decimalPlaces' => 2,
            'content' => null,
            'locale' => 'de_CH',
            'expected' => null,
        ];

        yield 'Column type is decimal and content is a numeric string, content is formatted' => [
            'type' => FieldTypeEnumeration::DECIMAL,
            'decimalPlaces' => 2,
            'content' => '123456.789',
            'locale' => 'de_DE',
            'expected' => '123.456,79',
        ];

        yield 'Column type is decimal and content is a non-numeric string, content is not changed' => [
            'type' => FieldTypeEnumeration::DECIMAL,
            'decimalPlaces' => 2,
            'content' => 'some content',
            'locale' => 'de_DE',
            'expected' => 'some content',
        ];

        yield 'Column type is integer, content is not changed' => [
            'type' => FieldTypeEnumeration::INTEGER,
            'decimalPlaces' => 2,
            'content' => 123456789,
            'locale' => 'de_CH',
            'expected' => 123456789,
        ];
    }
}
