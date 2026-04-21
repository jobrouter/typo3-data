<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\DataProcessing;

use JobRouter\AddOn\Typo3Data\Domain\Converter\DatasetConverter;
use JobRouter\AddOn\Typo3Data\Domain\Demand\TableDemandFactory;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use JobRouter\AddOn\Typo3Data\Extension;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * @internal
 */
#[AutoconfigureTag(
    name: 'data.processor',
    attributes: [
        'identifier' => 'jobrouter-data-table',
    ],
)]
final readonly class TableProcessor implements DataProcessorInterface
{
    public function __construct(
        private DatasetConverter $datasetConverter,
        private TableDemandFactory $tableDemandFactory,
        private TableRepository $tableRepository,
    ) {}

    /**
     * @param array<string, mixed> $contentObjectConfiguration
     * @param array<string, mixed> $processorConfiguration
     * @param array<string, mixed> $processedData
     * @return array<string, mixed>
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $request = $cObj->getRequest();

        try {
            $tableUid = (int) ($processedData['data']['tx_jobrouterdata_table'] ?? 0);
            $table = $this->tableRepository->findByUid($tableUid);
            $tableDemand = $this->tableDemandFactory->create($table);

            $locale = $request->getAttribute('language')->getLocale()->getName();
            $processedData['table'] = $tableDemand;
            $processedData['rows'] = $this->datasetConverter->convertFromJsonToArray($table, $locale);
            $this->addCacheTag($tableUid, $request);
        } catch (TableNotFoundException) {
        }

        return $processedData;
    }

    private function addCacheTag(int $tableUid, ServerRequestInterface $request): void
    {
        /** @var CacheDataCollector|null $cacheCollector */
        $cacheCollector = $request->getAttribute('frontend.cache.collector');
        $cacheCollector?->addCacheTags(
            new CacheTag(\sprintf(Extension::CACHE_TAG_TABLE_TEMPLATE, $tableUid)),
        );
    }
}
