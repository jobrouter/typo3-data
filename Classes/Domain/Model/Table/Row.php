<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Model\Table;

class Row
{
    /**
     * @var Cell[]
     */
    protected $cells = [];

    /**
     * @return Cell[]
     */
    public function getCells(): iterable
    {
        return $this->cells;
    }

    /**
     * @param Cell[] $cells
     */
    public function setCells(iterable $cells): void
    {
        $this->cells = $cells;
    }

    public function addCell(Cell $cell): void
    {
        $this->cells[] = $cell;
    }
}
