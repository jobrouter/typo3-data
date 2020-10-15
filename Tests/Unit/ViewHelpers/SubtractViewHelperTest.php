<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\ViewHelpers;

class SubtractViewHelperTest extends ViewHelperTestCase
{
    protected const VIEWHELPER_TEMPLATE = '<jobRouterData:subtract minuend="{minuend}" subtrahend="{subtrahend}"/>';

    /**
     * @test
     * @dataProvider dataProvider
     * @param int $minuend
     * @param int $subtrahend
     * @param int $expected
     */
    public function contentWithTypeTextIsRenderedCorrectly(int $minuend, int $subtrahend, int $expected): void
    {
        $actual = $this->renderTemplate(
            static::VIEWHELPER_TEMPLATE,
            ['minuend' => $minuend, 'subtrahend' => $subtrahend]
        );

        self::assertSame($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [
                42,
                42,
                0,
            ],
            [
                42,
                13,
                29,
            ],
            [
                31,
                58,
                -27,
            ],
        ];
    }
}
