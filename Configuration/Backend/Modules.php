<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Controller\TableListController;
use Brotkrueml\JobRouterData\Extension;

return [
    // "Data" module
    Extension::MODULE_NAME => [
        'parent' => 'jobrouter',
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/jobrouter/data',
        'labels' => Extension::LANGUAGE_PATH_BACKEND_MODULE,
        'iconIdentifier' => 'jobrouter-module-data',
        'routes' => [
            '_default' => [
                'target' => TableListController::class . '::handleRequest',
            ],
        ],
    ],
];
