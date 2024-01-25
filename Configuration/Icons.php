<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Data\Extension;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'jobrouter-module-data' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/jobrouter-data-module.svg',
    ],
    'jobrouter-data-ce-table' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/ce-table.svg',
    ],
    'jobrouter-data-toolbar' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/jobrouter-data-toolbar.svg',
    ],
];
