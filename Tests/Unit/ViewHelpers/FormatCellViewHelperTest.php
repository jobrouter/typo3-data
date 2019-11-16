<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\ViewHelpers;

use Brotkrueml\JobRouterData\Domain\Model\Table\Cell;
use Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration;
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
        $cell->setType(ColumnTypeEnumeration::TEXT);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('some text content', $actual);
    }

    /**
     * @test
     */
    public function contentWithTypeIntegerIsRenderedCorrectly(): void
    {
        $cell = new Cell();
        $cell->setContent(42);
        $cell->setType(ColumnTypeEnumeration::INTEGER);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('42', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function contentWithTypeDecimalIsRenderedCorrectly(): void
    {
        $languageServiceMock = $this->initialiseLanguageServiceMock();
        $languageServiceMock
            ->expects($this->at(0))
            ->method('sL')
            ->with('LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:decimal_point')
            ->willReturn('#');
        $languageServiceMock
            ->expects($this->at(1))
            ->method('sL')
            ->with('LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:thousands_separator')
            ->willReturn('$');

        $cell = new Cell();
        $cell->setContent(1234.56789);
        $cell->setType(ColumnTypeEnumeration::DECIMAL);
        $cell->setDecimalPlaces(3);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('1$234#568', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function contentWithTypeDateIsRenderedCorrectly(): void
    {
        $languageServiceMock = $this->initialiseLanguageServiceMock();
        $languageServiceMock
            ->expects($this->once())
            ->method('sL')
            ->with('LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:date')
            ->willReturn('d+m+Y');

        $cell = new Cell();
        $cell->setContent('2019-05-15T00:00:00+00:00');
        $cell->setType(ColumnTypeEnumeration::DATE);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('15+05+2019', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function contentWithTypeDateTimeIsRenderedCorrectly(): void
    {
        $languageServiceMock = $this->initialiseLanguageServiceMock();
        $languageServiceMock
            ->expects($this->once())
            ->method('sL')
            ->with('LLL:EXT:jobrouter_data/Resources/Private/Language/Format.xlf:datetime')
            ->willReturn('d+m+Y H-i-s');

        $cell = new Cell();
        $cell->setContent('2019-05-15T12:34:56+09:00');
        $cell->setType(ColumnTypeEnumeration::DATETIME);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('15+05+2019 12-34-56', $actual);
    }

    /**
     * @test
     */
    public function invalidContentWithTypeDateIsReturningOriginalContent(): void
    {
        $cell = new Cell();
        $cell->setContent('some invalid date content');
        $cell->setType(ColumnTypeEnumeration::DATE);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('some invalid date content', $actual);
    }

    /**
     * @test
     */
    public function invalidContentWithTypeDateTimeIsReturningOriginalContent(): void
    {
        $cell = new Cell();
        $cell->setContent('some invalid datetime content');
        $cell->setType(ColumnTypeEnumeration::DATETIME);

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => $cell]);

        $this->assertSame('some invalid datetime content', $actual);
    }

    /**
     * @test
     */
    public function givenCellIsNotADomainModelThrowsException(): void
    {
        $this->expectException(ViewHelper\Exception::class);
        $this->expectExceptionCode(1567619441);

        $this->renderTemplate(static::VIEWHELPER_TEMPLATE, ['cell' => new \stdClass()]);
    }
}
