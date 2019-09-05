<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Domain\Model;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Dataset model
 */
class Dataset extends AbstractEntity
{
    /** @var int */
    protected $jrid = 0;

    /** @var string */
    protected $dataset = '';

    public function getJrid(): int
    {
        return $this->jrid;
    }

    public function setJrid(int $jrid): void
    {
        $this->jrid = $jrid;
    }

    public function getDataset(): string
    {
        return $this->dataset;
    }

    public function setDataset(string $dataset): void
    {
        $this->dataset = $dataset;
    }
}
