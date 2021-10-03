<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'jobrouter_data_table_check' => [
        'path' => '/jobrouter/data/table/check',
        'target' => \Brotkrueml\JobRouterData\Controller\TableAjaxController::class . '::checkAction',
    ],
];
