<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\EventListener;

use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;

final class DecimalFormatter
{
    public function __invoke(ModifyColumnContentEvent $event): void
    {
        $column = $event->getColumn();
        if ($column->getType() !== FieldTypeEnumeration::DECIMAL) {
            return;
        }

        $formatter = new \NumberFormatter($event->getLocale(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $column->getDecimalPlaces());
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $column->getDecimalPlaces());
        $event->setContent($formatter->format($event->getContent()));
    }
}
