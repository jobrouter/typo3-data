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
     */
    public function contentWithTypeTextIsRenderedCorrectly(int $minuend, int $subtrahend, int $expected): void
    {
        $actual = $this->renderTemplate(
            static::VIEWHELPER_TEMPLATE,
            [
                'minuend' => $minuend,
                'subtrahend' => $subtrahend,
            ]
        );

        self::assertSame($expected, $actual);
    }

    public function dataProvider(): iterable
    {
        yield [
            42,
            42,
            0,
        ];

        yield [
            42,
            13,
            29,
        ];

        yield [
            31,
            58,
            -27,
        ];
    }
}
