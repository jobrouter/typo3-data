<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\EventListener;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;

final class DecimalFormatter
{
    public function __invoke(ModifyColumnContentEvent $event): void
    {
        $column = $event->getColumn();
        if ($column->getType() !== FieldType::Decimal->value) {
            return;
        }

        $content = $event->getContent();
        if ($content === null) {
            return;
        }

        if (\is_string($content) && \is_numeric($content)) {
            $content = (float)$content;
        }

        if (\is_string($content)) {
            return;
        }

        $formatter = new \NumberFormatter($event->getLocale(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $column->getDecimalPlaces());
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $column->getDecimalPlaces());
        $formattedContent = $formatter->format($content);
        if ($formattedContent !== false) {
            $event->setContent($formattedContent);
        }
    }
}
