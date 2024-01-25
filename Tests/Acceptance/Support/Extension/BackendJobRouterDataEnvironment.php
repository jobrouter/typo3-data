<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Acceptance\Support\Extension;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

/**
 * Load various core extensions and JobRouter Data
 */
class BackendJobRouterDataEnvironment extends BackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide
     *
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'install',
            'frontend',
            'recordlist',
            'form',
        ],
        'testExtensionsToLoad' => [
            'typo3conf/ext/jobrouter_base',
            'typo3conf/ext/jobrouter_connector',
            'typo3conf/ext/jobrouter_data',
        ],
        'csvDatabaseFixtures' => [
            __DIR__ . '/../../Fixtures/be_users.csv',
            __DIR__ . '/../../Fixtures/tx_jobrouterconnector_domain_model_connection.csv',
        ],
        'pathsToLinkInTestInstance' => [
            'typo3conf/ext/jobrouter_data/Tests/Acceptance/Fixtures/jobrouterkey' => '.jobrouterkey',
        ],
    ];
}
