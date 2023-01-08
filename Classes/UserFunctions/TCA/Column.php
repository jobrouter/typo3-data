<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\UserFunctions\TCA;

use TYPO3\CMS\Core\Localization\LanguageService;

final class Column
{
    private const L10N_TYPE_PREFIX = 'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.';

    /**
     * @param array{table: string, row: array<string, int|string|null>, title: string} $parameters
     */
    public function getLabel(array &$parameters): void
    {
        $label = (string)($parameters['row']['label'] ?? '');
        if (\str_starts_with($label, 'LLL:')) {
            $label = $this->getLanguageService()->sL($label);
        }
        if ($label === '') {
            $label = $parameters['row']['name'];
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
