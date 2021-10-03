<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\ViewHelpers;

use Brotkrueml\JobRouterData\Domain\Model\Column;
use TYPO3Fluid\Fluid\Core\ViewHelper;

class ColumnLabelViewHelperTest extends ViewHelperTestCase
{
    protected const VIEWHELPER_TEMPLATE = '<jobRouterData:columnLabel column="{column}"/>';

    /**
     * @test
     */
    public function withOnlyNameSetTheNameIsReturned(): void
    {
        $column = new Column();
        $column->setName('someColumnLabel');

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'column' => $column,
        ]);

        self::assertSame('someColumnLabel', $actual);
    }

    /**
     * @test
     */
    public function withLabelSetTheLabelIsReturned(): void
    {
        $column = new Column();
        $column->setName('someColumnLabel');
        $column->setLabel('Some column label');

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'column' => $column,
        ]);

        self::assertSame('Some column label', $actual);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function withLocalisedLabelSetTheLocalisedLabelIsReturned(): void
    {
        $languageServiceStub = $this->initialiseLanguageServiceStub();
        $languageServiceStub
            ->method('sL')
            ->with('LLL:EXT:some_extension/Resources/Private/Language/locallang.xml:some-localised-column-label')
            ->willReturn('the localised column label');

        $column = new Column();
        $column->setName('someColumnLabel');
        $column->setLabel('LLL:EXT:some_extension/Resources/Private/Language/locallang.xml:some-localised-column-label');

        $actual = $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'column' => $column,
        ]);

        self::assertSame('the localised column label', $actual);
    }

    /**
     * @test
     */
    public function givenColumnIsNotADomainModelThrowsException(): void
    {
        $this->expectException(ViewHelper\Exception::class);
        $this->expectExceptionCode(1567518752);

        $this->renderTemplate(static::VIEWHELPER_TEMPLATE, [
            'column' => new \stdClass(),
        ]);
    }
}
