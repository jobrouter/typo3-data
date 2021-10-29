<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Dataset extends AbstractEntity
{
    /**
     * @var int
     */
    protected $tableUid = 0;

    /**
     * @var int
     */
    protected $jrid = 0;

    /**
     * @var string
     */
    protected $dataset = '';

    /**
     * @var array<string, mixed>|null
     */
    protected $decodedDataset;

    public function getTableUid(): int
    {
        return $this->tableUid;
    }

    public function setTableUid(int $tableUid): void
    {
        $this->tableUid = $tableUid;
    }

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
        $this->decodedDataset = null;
    }

    public function getDatasetContentForColumn(string $column): ?string
    {
        if ($this->decodedDataset === null) {
            try {
                $this->decodedDataset = \json_decode($this->dataset, true, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
        }

        return $this->decodedDataset[$column] ?? null;
    }
}
