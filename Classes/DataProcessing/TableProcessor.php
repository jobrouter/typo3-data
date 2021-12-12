<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\DataProcessing;

use Brotkrueml\JobRouterData\Cache\Cache;
use Brotkrueml\JobRouterData\Domain\Converter\DatasetConverter;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * @phpstan-type ProcessedData array{data: array<string, int|string|null>, current: null, table?: Table, rows?: array<int, array<string, float|int|string>>}
 * @internal
 */
final class TableProcessor implements DataProcessorInterface
{
    /**
     * @var DatasetConverter
     */
    private $datasetConverter;

    /**
     * @var FlexFormService
     */
    private $flexFormService;

    /**
     * @var TableRepository
     */
    private $tableRepository;

    /**
     * @var ContentObjectRenderer
     */
    private $cObj;

    /**
     * @var ProcessedData
     */
    private $processedData;

    public function __construct(
        DatasetConverter $datasetConverter = null,
        FlexFormService $flexFormService = null,
        TableRepository $tableRepository = null
    ) {
        $this->datasetConverter = $datasetConverter ?? GeneralUtility::makeInstance(DatasetConverter::class);
        $this->flexFormService = $flexFormService ?? GeneralUtility::makeInstance(FlexFormService::class);
        $this->tableRepository = $tableRepository ?? GeneralUtility::makeInstance(TableRepository::class);
    }

    /**
     * @param array<string, mixed> $contentObjectConfiguration
     * @param array<string, mixed> $processorConfiguration
     * @param ProcessedData $processedData
     * @return array<string, mixed>
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $this->cObj = $cObj;
        $this->processedData = $processedData;

        $flexForm = $this->flexFormService->convertFlexFormContentToArray($cObj->data['pi_flexform']);
        $tableUid = (int)($flexForm['table'] ?? 0);
        if ($tableUid > 0) {
            $this->enrichProcessedDataWithTableInformation($tableUid);
        }

        return $this->processedData;
    }

    private function enrichProcessedDataWithTableInformation(int $tableUid): void
    {
        $locale = $this->cObj->getRequest()->getAttribute('language')->getLocale();
        /** @var Table $table */
        $table = $this->tableRepository->findByIdentifier($tableUid);
        $this->processedData['table'] = $table;
        $this->processedData['rows'] = $this->datasetConverter->convertFromJsonToArray($table, $locale);
        Cache::addCacheTagByTable($tableUid);
    }
}
