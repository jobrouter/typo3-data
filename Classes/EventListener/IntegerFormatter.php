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

final class IntegerFormatter
{
    public function __invoke(ModifyColumnContentEvent $event): void
    {
        if ($event->getColumn()->getType() !== FieldTypeEnumeration::INTEGER) {
            return;
        }

        $content = $event->getContent();
        if ($content === null) {
            return;
        }

        if (\is_string($content) && \is_numeric($content)) {
            $content = (int)$content;
        }

        if (\is_string($content)) {
            return;
        }

        $formatter = new \NumberFormatter($event->getLocale(), \NumberFormatter::DEFAULT_STYLE);
        $event->setContent($formatter->format($content));
    }
}
