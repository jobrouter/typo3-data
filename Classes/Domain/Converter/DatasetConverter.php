<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Converter;

use Brotkrueml\JobRouterData\Domain\Entity\Column;
use Brotkrueml\JobRouterData\Domain\Entity\Table;
use Brotkrueml\JobRouterData\Domain\Repository\ColumnRepository;
use Brotkrueml\JobRouterData\Domain\Repository\DatasetRepository;
use Brotkrueml\JobRouterData\Event\ModifyColumnContentEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class DatasetConverter
{
    private const UNFORMATTED_FIELD_NAME_PREFIX = '_original_';

    public function __construct(
        private readonly ColumnRepository $columnRepository,
        private readonly DatasetRepository $datasetRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @return list<array<string, float|int|string>>
     */
    public function convertFromJsonToArray(Table $table, string $locale): array
    {
        $columns = $this->columnRepository->findByTableUid($table->uid);
        $datasets = $this->datasetRepository->findByTableUid($table->uid);

        $rows = [];
        foreach ($datasets as $dataset) {
            $row = [];
            foreach ($columns as $column) {
                $columnName = $column->name;
                if (isset($dataset->dataset[$columnName])) {
                    $row[self::UNFORMATTED_FIELD_NAME_PREFIX . $columnName] = $dataset->dataset[$column->name];
                    if ($row[self::UNFORMATTED_FIELD_NAME_PREFIX . $columnName] === null || $row[self::UNFORMATTED_FIELD_NAME_PREFIX . $columnName] === '') {
                        $row[$columnName] = '';
                        continue;
                    }

                    $event = new ModifyColumnContentEvent($table, $column, $row[self::UNFORMATTED_FIELD_NAME_PREFIX . $columnName], $locale);
                    /** @var ModifyColumnContentEvent $event */
                    $event = $this->eventDispatcher->dispatch($event);
                    $row[$column->name] = $event->getContent();
                }
            }
            $rows[] = $row;
        }

        return $this->sortRowsByColumns($columns, $rows);
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
                    $order = $column->sortingOrder === 'desc' ? -1 : 1;
                    $result = ($a[self::UNFORMATTED_FIELD_NAME_PREFIX . $column->name] <=> $b[self::UNFORMATTED_FIELD_NAME_PREFIX . $column->name]) * $order;
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
            \array_filter(
                $columns,
                static fn(Column $column): bool => $column->sortingPriority > 0,
            ),
        );
        \usort(
            $columnsToSortBy,
            static fn(Column $a, Column $b): int => $a->sortingPriority <=> $b->sortingPriority,
        );

        return $columnsToSortBy;
    }
}
