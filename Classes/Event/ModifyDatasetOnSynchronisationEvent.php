<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Event;

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Exception\ModifyDatasetException;

final class ModifyDatasetOnSynchronisationEvent
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var array<string, float|int|string|bool|null>
     */
    private $dataset;

    /**
     * @var bool
     */
    private $rejected = false;

    /**
     * @param array<string, float|int|string|bool|null> $dataset
     */
    public function __construct(Table $table, array $dataset)
    {
        $this->table = $table;
        $this->dataset = $dataset;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @return array<string, float|int|string|bool|null>
     */
    public function getDataset(): array
    {
        return $this->dataset;
    }

    /**
     * @param array<string, float|int|string|bool|null> $dataset
     */
    public function setDataset(array $dataset): void
    {
        $this->compareOriginalWithGivenDatasetKeys($dataset);
        $this->compareOriginalJridWithGivenJrid($dataset);
        $this->dataset = $dataset;
    }

    /**
     * @param array<string, float|int|string|bool|null> $dataset
     */
    private function compareOriginalWithGivenDatasetKeys(array $dataset): void
    {
        $originalKeys = \array_keys($this->dataset);
        $newKeys = \array_keys($dataset);
        if ($originalKeys !== $newKeys) {
            throw new ModifyDatasetException(
                \sprintf(
                    'Given dataset keys "%s" differ from original dataset keys "%s" when modfying dataset for table with handle "%s"',
                    \implode(', ', $newKeys),
                    \implode(', ', $originalKeys),
                    $this->table->getHandle()
                ),
                1639132693
            );
        }
    }

    /**
     * @param array<string, float|int|string|bool|null> $dataset
     */
    private function compareOriginalJridWithGivenJrid(array $dataset): void
    {
        if ($dataset['jrid'] !== $this->dataset['jrid']) {
            throw new ModifyDatasetException(
                \sprintf(
                    'jrid must not be overriden for table with handle "%s", original jrid is "%d", new jrid id "%d"',
                    $this->table->getHandle(),
                    $this->dataset['jrid'],
                    $dataset['jrid']
                ),
                1639132877
            );
        }
    }

    public function setRejected(): void
    {
        $this->rejected = true;
    }

    public function isRejected(): bool
    {
        return $this->rejected;
    }
}
