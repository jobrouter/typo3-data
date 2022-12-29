<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\DataProcessing;

use Brotkrueml\JobRouterData\Domain\Converter\DatasetConverter;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * @internal
 */
final class TableProcessor implements DataProcessorInterface
{
    private ContentObjectRenderer $cObj;
    private array $processedData;

    public function __construct(
        private readonly DatasetConverter $datasetConverter,
        private readonly FlexFormService $flexFormService,
        private readonly TableRepository $tableRepository
    ) {
    }

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
        $this->addCacheTag($tableUid);
    }

    private function addCacheTag(int $tableUid): void
    {
        $cacheTags = [
            \sprintf(Extension::CACHE_TAG_TABLE_TEMPLATE, $tableUid),
        ];
        $this->cObj->getRequest()->getAttribute('frontend.controller')->addCacheTags($cacheTags);
    }
}
