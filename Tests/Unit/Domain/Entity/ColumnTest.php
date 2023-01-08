<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterData\Domain\Entity\Column;
use PHPUnit\Framework\TestCase;

final class ColumnTest extends TestCase
{
    /**
     * @test
     */
    public function fromArray(): void
    {
        $actual = Column::fromArray([
            'uid' => '42',
            'name' => 'some name',
            'label' => 'some label',
            'type' => '1',
            'decimal_places' => '2',
            'field_size' => '50',
            'alignment' => 'left',
            'sorting_priority' => '10',
            'sorting_order' => 'ASC',
        ]);

        self::assertInstanceOf(Column::class, $actual);
        self::assertSame(42, $actual->uid);
        self::assertSame('some name', $actual->name);
        self::assertSame('some label', $actual->label);
        self::assertSame(1, $actual->type);
        self::assertSame(2, $actual->decimalPlaces);
        self::assertSame(50, $actual->fieldSize);
        self::assertSame('left', $actual->alignment);
        self::assertSame(10, $actual->sortingPriority);
        self::assertSame('ASC', $actual->sortingOrder);
    }
}
