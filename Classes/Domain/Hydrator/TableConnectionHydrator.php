<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Hydrator;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
use Brotkrueml\JobRouterConnector\Domain\Repository\ConnectionRepository;
use Brotkrueml\JobRouterConnector\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterData\Domain\Entity\Table;

final class TableConnectionHydrator
{
    /**
     * @var array<int, Connection|null>
     */
    private array $connectionsCache = [];

    public function __construct(
        private readonly ConnectionRepository $connectionRepository,
    ) {
    }

    public function hydrate(Table $table): Table
    {
        if (! isset($this->connectionsCache[$table->connectionUid])) {
            try {
                $this->connectionsCache[$table->connectionUid] = $this->connectionRepository->findByUid($table->connectionUid, true);
            } catch (ConnectionNotFoundException) {
                $this->connectionsCache[$table->connectionUid] = null;
            }
        }

        if (! $this->connectionsCache[$table->connectionUid] instanceof Connection) {
            return $table;
        }

        return $table->withConnection($this->connectionsCache[$table->connectionUid]);
    }
}
