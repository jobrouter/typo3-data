<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\ViewHelpers;

use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterData\Domain\Model\Table\Cell;
use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper;

final class FormatCellViewHelper extends ViewHelper\AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('cell', 'object', 'The column domain model', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var Cell $cell */
        $cell = $arguments['cell'];

        if (!$cell instanceof Cell) {
            throw new ViewHelper\Exception(
                \sprintf(
                    'Argument "cell" is not an instance of "%s"',
                    Cell::class
                ),
                1567619441
            );
        }

        return static::formatContent($cell);
    }

    private static function formatContent(Cell $cell): string
    {
        $type = $cell->getType();
        $content = $cell->getContent();

        if (FieldTypeEnumeration::DATE === $type) {
            try {
                $date = new \DateTime($content);

                return $date->format(static::localise('date'));
            } catch (\Exception $e) {
                return (string)$content;
            }
        }

        if (FieldTypeEnumeration::DATETIME === $type) {
            try {
                $date = new \DateTime($content);

                return $date->format(static::localise('datetime'));
            } catch (\Exception $e) {
                return (string)$content;
            }
        }

        if (FieldTypeEnumeration::DECIMAL === $type) {
            return \number_format(
                (float)$content,
                $cell->getDecimalPlaces(),
                static::localise('decimal_point'),
                static::localise('thousands_separator')
            );
        }

        return (string)$content;
    }

    private static function localise(string $key): string
    {
        return static::getLanguageService()->sL(Extension::LANGUAGE_PATH_FORMAT . ':' . $key);
    }

    private static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
