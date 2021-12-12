<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Converter;

use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class DatasetConverter
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return list<array<string, float|int|string>>
     */
    public function convertFromJsonToArray(Table $table, string $locale): array
    {
        $rows = [];
        foreach ($table->getDatasets() as $datasetJson) {
            $datasetArray = \json_decode($datasetJson->getDataset(), true, 512, \JSON_THROW_ON_ERROR);

            $row = [];
            foreach ($table->getColumns() as $column) {
                $columnName = $column->getName();
                if ($datasetArray[$columnName] ?? false) {
                    $row['_original_' . $columnName] = $datasetArray[$column->getName()];
                    if ($row['_original_' . $columnName] === null || $row['_original_' . $columnName] === '') {
                        $row[$columnName] = '';
                        continue;
                    }

                    $event = new ModifyColumnContentEvent($table, $column, $row['_original_' . $columnName], $locale);
                    /** @var ModifyColumnContentEvent $event */
                    $event = $this->eventDispatcher->dispatch($event);
                    $row[$column->getName()] = $event->getContent();
                }
            }
            $rows[] = $row;
        }

        return $this->sortRowsByColumns($table->getColumns()->toArray(), $rows);
    }

    /**
     * @param Column[] $columns
     * @param list<array<string, float|int|string>> $rows
     * @return list<array<string, float|int|string>>
     */
    public function sortRowsByColumns(array $columns, array $rows): array
    {
        $columnsToSortBy = $this->getSortingColumns($columns);
        if ($columnsToSortBy !== []) {
            \usort($rows, static function (array $a, array $b) use ($columnsToSortBy): int {
                foreach ($columnsToSortBy as $column) {
                    $order = $column->getSortingOrder() === 'desc' ? -1 : 1;
                    $result = ($a['_original_' . $column->getName()] <=> $b['_original_' . $column->getName()]) * $order;
                    if ($result !== 0) {
                        return $result;
                    }
                }

                return 0;
            });
        }

        return $rows;
    }

    /**
     * @param Column[] $columns
     * @return Column[]
     */
    public function getSortingColumns(array $columns): array
    {
        $columnsToSortBy = \array_values(
            \array_filter($columns, static function (Column $column): bool {
                return $column->getSortingPriority() > 0;
            })
        );
        \usort($columnsToSortBy, static function (Column $a, Column $b): int {
            return $a->getSortingPriority() <=> $b->getSortingPriority();
        });

        return $columnsToSortBy;
    }
}
