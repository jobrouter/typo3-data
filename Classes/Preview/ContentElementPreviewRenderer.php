<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Preview;

use JobRouter\AddOn\Typo3Data\Domain\Converter\DatasetConverter;
use JobRouter\AddOn\Typo3Data\Domain\Demand\TableDemandFactory;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use JobRouter\AddOn\Typo3Data\Extension;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class ContentElementPreviewRenderer extends StandardContentPreviewRenderer
{
    public function __construct(
        private readonly DatasetConverter $datasetConverter,
        private readonly SiteFinder $siteFinder,
        private readonly TableDemandFactory $tableDemandFactory,
        private readonly TableRepository $tableRepository,
    ) {}

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename('EXT:' . Extension::KEY . '/Resources/Private/Templates/Preview/ContentElement.html');

        $record = $item->getRecord();

        $flexForm = GeneralUtility::xml2array($record['pi_flexform']);
        $tableId = (int)$this->getValueFromFlexform($flexForm, 'table');

        try {
            $table = $this->tableRepository->findByUid($tableId);
            $site = $this->siteFinder->getSiteByPageId($record['pid']);
            // locale is casted to a string as in v12 the locale is an object with a __toString() method (in v11 it is a string)
            // @todo Remove the cast when compatibility with TYPO3 v11 is dropped
            $locale = (string)$site->getLanguageById($record['sys_language_uid'])->getLocale();

            $view->assignMultiple([
                'tableDemand' => $this->tableDemandFactory->create($table),
                'rows' => $this->datasetConverter->convertFromJsonToArray($table, $locale),
            ]);
        } catch (TableNotFoundException) {
        }

        return $this->linkEditContent($view->render(), $item->getRecord());
    }

    /**
     * @param array<string, array<string, mixed>> $flexForm
     */
    private function getValueFromFlexform(array $flexForm, string $key, string $sheet = 'sDEF'): ?string
    {
        return $flexForm['data'][$sheet]['lDEF'][$key]['vDEF'] ?? null;
    }
}
