<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\ViewHelpers;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Model\Column;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper;

class ColumnLabelViewHelper extends ViewHelper\AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('column', 'object', 'The column domain model', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var Column $column */
        $column = $arguments['column'];

        if (!$column instanceof Column) {
            throw new ViewHelper\Exception(
                \sprintf(
                    'Argument "column" is not an instance of "%s"',
                    Column::class
                ),
                1567518752
            );
        }

        $label = $column->getLabel();
        if ($label) {
            if (strpos($label, 'LLL:') === 0) {
                $translatedLabel = static::getLanguageService()->sL($label);

                if ($translatedLabel) {
                    return $translatedLabel;
                }
            } else {
                return $column->getLabel();
            }
        }

        return $column->getName();
    }

    private static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
