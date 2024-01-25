<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Helper\Entity;

use JobRouter\AddOn\Typo3Connector\Domain\Entity\Connection;

final class ConnectionBuilder
{
    public function build(int $uid, string $baseUrl = '', string $username = '', string $password = ''): Connection
    {
        return Connection::fromArray([
            'uid' => 1,
            'name' => 'some name',
            'handle' => 'some_handle',
            'base_url' => $baseUrl,
            'username' => $username,
            'password' => $password,
            'timeout' => 0,
            'verify' => true,
            'proxy' => '',
            'jobrouter_version' => '',
            'disabled' => false,
        ]);
    }
}
