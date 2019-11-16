<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\ViewHelpers;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper;

final class SubtractViewHelper extends ViewHelper\AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('minuend', 'int', 'The minuend', true);
        $this->registerArgument('subtrahend', 'int', 'The subtrahend', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $minuend = (int)$arguments['minuend'];
        $subtrahend = (int)$arguments['subtrahend'];

        return $minuend - $subtrahend;
    }
}
