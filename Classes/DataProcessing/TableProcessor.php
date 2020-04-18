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
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * @internal
 */
final class TableProcessor implements DataProcessorInterface
{
    /** @var FlexFormService */
    private $flexFormService;

    /** @var TableRepository */
    private $tableRepository;

    public function __construct(FlexFormService $flexFormService = null, TableRepository $tableRepository = null)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->flexFormService = $flexFormService ?? $objectManager->get(FlexFormService::class);
        $this->tableRepository = $tableRepository ?? $objectManager->get(TableRepository::class);
    }

    public function process(
        ContentObjectRenderer $contentObjectRenderer,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $flexForm = $this->flexFormService->convertFlexFormContentToArray($contentObjectRenderer->data['pi_flexform']);

        $tableUid = (int)($flexForm['table'] ?? 0);

        if (!empty($tableUid)) {
            $processedData['table'] = $this->tableRepository->findByIdentifier($tableUid);
            Cache::addCacheTagByTable($tableUid);
        }

        return $processedData;
    }
}
