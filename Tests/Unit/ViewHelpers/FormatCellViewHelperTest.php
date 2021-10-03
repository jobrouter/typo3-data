<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\ViewHelpers;

use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterData\Domain\Model\Table\Cell;
use TYPO3Fluid\Fluid\Core\ViewHelper;

class FormatCellViewHelperTest extends ViewHelperTestCase
{
    protected const VIEWHELPER_TEMPLATE = '<jobRouterData:formatCell cell="{cell}"/>';

    /**
     * @test
     */
    public function contentWithTypeTextIsRenderedCorrectly(): void
    {
        $cell = new Cell();
        $cell->setContent('some text content');
        $cell->setType(FieldTypeEnumeration::TEXT);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('some text content', $actual);
    }

    /**
     * @test
     */
    public function contentWithTypeIntegerIsRenderedCorrectly(): void
    {
        $cell = new Cell();
        $cell->setContent(42);
        $cell->setType(FieldTypeEnumeration::INTEGER);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('42', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function contentWithTypeDecimalIsRenderedCorrectly(): void
    {
        $languageMap = [
            ['LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:thousands_separator', '$'],
            ['LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:decimal_point', '#'],
        ];

        $languageServiceStub = $this->initialiseLanguageServiceStub();
        $languageServiceStub
            ->method('sL')
            ->willReturnMap($languageMap);

        $cell = new Cell();
        $cell->setContent(1234.56789);
        $cell->setType(FieldTypeEnumeration::DECIMAL);
        $cell->setDecimalPlaces(3);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('1$234#568', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function contentWithTypeDateIsRenderedCorrectly(): void
    {
        $languageServiceStub = $this->initialiseLanguageServiceStub();
        $languageServiceStub
            ->method('sL')
            ->with('LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:date')
            ->willReturn('d+m+Y');

        $cell = new Cell();
        $cell->setContent('2019-05-15T00:00:00+00:00');
        $cell->setType(FieldTypeEnumeration::DATE);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('15+05+2019', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function contentWithTypeDateTimeIsRenderedCorrectly(): void
    {
        $languageServiceStub = $this->initialiseLanguageServiceStub();
        $languageServiceStub
            ->method('sL')
            ->with('LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:datetime')
            ->willReturn('d+m+Y H-i-s');

        $cell = new Cell();
        $cell->setContent('2019-05-15T12:34:56+09:00');
        $cell->setType(FieldTypeEnumeration::DATETIME);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('15+05+2019 12-34-56', $actual);
    }

    /**
     * @test
     */
    public function invalidContentWithTypeDateIsReturningOriginalContent(): void
    {
        $cell = new Cell();
        $cell->setContent('some invalid date content');
        $cell->setType(FieldTypeEnumeration::DATE);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('some invalid date content', $actual);
    }

    /**
     * @test
     */
    public function invalidContentWithTypeDateTimeIsReturningOriginalContent(): void
    {
        $cell = new Cell();
        $cell->setContent('some invalid datetime content');
        $cell->setType(FieldTypeEnumeration::DATETIME);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => $cell,
        ]);

        self::assertSame('some invalid datetime content', $actual);
    }

    /**
     * @test
     */
    public function givenCellIsNotADomainModelThrowsException(): void
    {
        $this->expectException(ViewHelper\Exception::class);
        $this->expectExceptionCode(1567619441);

        $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'cell' => new \stdClass(),
        ]);
    }
}
