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

final class DateFormatter
{
    public function __invoke(ModifyColumnContentEvent $event): void
    {
        if ($event->getColumn()->getType() !== FieldTypeEnumeration::DATE) {
            return;
        }

        $formatter = new \IntlDateFormatter($event->getLocale(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
        $formattedDate = $formatter->format(new \DateTimeImmutable((string)$event->getContent()));
        if ($formattedDate !== false) {
            $event->setContent($formattedDate);
        }
    }
}
