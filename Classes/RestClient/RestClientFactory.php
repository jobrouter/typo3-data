<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\RestClient;

use Brotkrueml\JobRouterClient\Client\ClientInterface;
use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @internal
 */
class RestClientFactory
{
    /**
     * @var string
     */
    private static $version;

    public function create(Connection $connection, ?int $lifetime = null): ClientInterface
    {
        return (new \Brotkrueml\JobRouterConnector\RestClient\RestClientFactory())
            ->create($connection, $lifetime, $this->getUserAgentAddition());
    }

    private function getUserAgentAddition(): string
    {
        if (!static::$version) {
            include ExtensionManagementUtility::extPath('jobrouter_data') . '/ext_emconf.php';
            static::$version = \array_pop($EM_CONF)['version'];
        }

        return \sprintf(
            'TYPO3-JobRouter-Data/%s (https://typo3-jobrouter.rtfd.io/projects/data/)',
            static::$version
        );
    }
}
