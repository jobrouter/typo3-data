<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\EventListener;

use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;
use JobRouter\AddOn\Typo3Data\Event\ModifyColumnContentEvent;

/**
 * @internal
 */
final class IntegerFormatter
{
    public function __invoke(ModifyColumnContentEvent $event): void
    {
        if ($event->getColumn()->type !== FieldType::Integer->value) {
            return;
        }

        $content = $event->getContent();
        if ($content === null) {
            return;
        }

        if (\is_string($content) && \is_numeric($content)) {
            $content = (int) $content;
        }

        if (\is_string($content)) {
            return;
        }

        $formatter = new \NumberFormatter($event->getLocale(), \NumberFormatter::DEFAULT_STYLE);
        $formattedContent = $formatter->format($content);
        if ($formattedContent !== false) {
            $event->setContent($formattedContent);
        }
    }
}
