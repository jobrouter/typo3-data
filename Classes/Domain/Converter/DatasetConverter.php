<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Converter;

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
     * @return list<array<string, float|int|string|null>>
     */
    public function convertFromJsonToArray(Table $table, string $locale): array
    {
        $rows = [];
        foreach ($table->getDatasets() as $datasetJson) {
            $datasetArray = \json_decode($datasetJson->getDataset(), true, 512, \JSON_THROW_ON_ERROR);

            $row = [];
            foreach ($table->getColumns() as $column) {
                if ($datasetArray[$column->getName()] ?? false) {
                    /** @var float|int|string|null $content */
                    $content = $datasetArray[$column->getName()];
                    if ($content === null || $content === '') {
                        $row[$column->getName()] = '';
                        continue;
                    }

                    $event = new ModifyColumnContentEvent($table, $column, $content, $locale);
                    /** @var ModifyColumnContentEvent $event */
                    $event = $this->eventDispatcher->dispatch($event);
                    $row[$column->getName()] = $event->getContent();
                }
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
