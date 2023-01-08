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
use Brotkrueml\JobRouterData\Domain\Repository\ColumnRepository;

final class TableColumnsHydrator
{
    public function __construct(
        private readonly ColumnRepository $columnRepository
    ) {
    }

    public function hydrate(Table $table): Table
    {
        return $table->withColumns($this->columnRepository->findByTableUid($table->uid));
    }
}
