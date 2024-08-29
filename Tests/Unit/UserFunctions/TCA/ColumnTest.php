<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\UserFunctions\TCA;

use JobRouter\AddOn\Typo3Data\UserFunctions\TCA\Column;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(Column::class)]
final class ColumnTest extends TestCase
{
    private Column $subject;

    protected function setUp(): void
    {
        $languageMap = [
            ['LLL:EXT:some_ext/file.xlf:translated_label', 'Translated label'],
            ['LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.42', 'Translated type'],
        ];

        $languageServiceStub = $this->createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->willReturnMap($languageMap);
        $GLOBALS['LANG'] = $languageServiceStub;

        $this->subject = new Column();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    /**
     * @param array<string, string|int> $parameters
     */
    #[Test]
    #[DataProvider('dataProviderForGetLabel')]
    public function getLabel(array $parameters, string $expected): void
    {
        $this->subject->getLabel($parameters);

        self::assertSame($expected, $parameters['title']);
    }

    public static function dataProviderForGetLabel(): iterable
    {
        yield 'Name field is used when label field is empty' => [
            'parameters' => [
                'row' => [
                    'name' => 'Some name',
                    'label' => '',
                ],
            ],
            'expected' => 'Some name',
        ];

        yield 'Label field is used' => [
            'parameters' => [
                'row' => [
                    'label' => 'Some label',
                ],
            ],
            'expected' => 'Some label',
        ];

        yield 'Label field is translated with starting LLL: prefix' => [
            'parameters' => [
                'row' => [
                    'label' => 'LLL:EXT:some_ext/file.xlf:translated_label',
                ],
            ],
            'expected' => 'Translated label',
        ];

        yield 'Translated field type is added to label' => [
            'parameters' => [
                'row' => [
                    'label' => 'Some label',
                    'type' => 42,
                ],
            ],
            'expected' => 'Some label (Translated type)',
        ];

        yield 'Without label and name given' => [
            'parameters' => [
                'row' => [
                    'type' => 42,
                ],
            ],
            'expected' => 'Unknown (Translated type)',
        ];
    }
}
