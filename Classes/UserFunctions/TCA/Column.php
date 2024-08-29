<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\UserFunctions\TCA;

use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * The class provides a customised label for a table column in backend form
 */
final class Column
{
    private const L10N_TYPE_PREFIX = 'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.';

    /**
     * @param array{table: string, row: array<string, int|string|null>, title: string} $parameters
     */
    public function getLabel(array &$parameters): void
    {
        $label = (string) ($parameters['row']['label'] ?? '');
        if (\str_starts_with($label, 'LLL:')) {
            $label = $this->getLanguageService()->sL($label);
        }
        if ($label === '') {
            // Since TYPO3 v13 this user function is triggered when changing the type of the column, providing an incomplete row.
            // Therefore, we add a fallback value. However, this fallback value is not displayed in the backend.
            $label = $parameters['row']['name'] ?? 'Unknown';
        }

        $type = isset($parameters['row']['type']) && \is_int($parameters['row']['type']) ? $parameters['row']['type'] : 0;
        if ($type > 0) {
            $label .= \sprintf(
                ' (%s)',
                $this->getLanguageService()->sL(self::L10N_TYPE_PREFIX . $type),
            );
        }

        $parameters['title'] = $label;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
