<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'jobrouter-module-data' => [
        'provider' => TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-module.svg',
    ],
    'jobrouter-data-ce-table' => [
        'provider' => TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/ce-table.svg',
    ],
    'jobrouter-data-toolbar' => [
        'provider' => TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:' . Brotkrueml\JobRouterData\Extension::KEY . '/Resources/Public/Icons/jobrouter-data-toolbar.svg',
    ],
];
