<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Hydrator;

use Brotkrueml\JobRouterData\Domain\Entity\Table;

final class TableRelationsHydrator
{
    public function __construct(
        private readonly TableColumnsHydrator $columnsHydrator,
        private readonly TableConnectionHydrator $connectionHydrator,
    ) {
    }

    public function hydrate(Table $table): Table
    {
        return $this->connectionHydrator->hydrate(
            $this->columnsHydrator->hydrate($table),
        );
    }

    /**
     * @param Table[] $tables
     * @return Table[]
     */
    public function hydrateMultiple(array $tables): array
    {
        return \array_map($this->hydrate(...), $tables);
    }
}
