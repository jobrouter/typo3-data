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

final class DateTimeFormatter
{
    public function __invoke(ModifyColumnContentEvent $event): void
    {
        if ($event->getColumn()->type !== FieldType::DateTime->value) {
            return;
        }

        $formatter = new \IntlDateFormatter($event->getLocale(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT);
        $formattedDateTime = $formatter->format(new \DateTimeImmutable((string)$event->getContent()));
        if ($formattedDateTime !== false) {
            $event->setContent($formattedDateTime);
        }
    }
}
