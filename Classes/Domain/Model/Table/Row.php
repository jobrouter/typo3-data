<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Domain\Model\Table;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class Row
{
    protected $cells = [];

    public function getCells(): iterable
    {
        return $this->cells;
    }

    public function setCells(iterable $cells): void
    {
        $this->cells = $cells;
    }

    public function addCell(Cell $cell): void
    {
        $this->cells[] = $cell;
    }
}
