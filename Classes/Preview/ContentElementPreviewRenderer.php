<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Preview;

use Brotkrueml\JobRouterData\Domain\Converter\DatasetConverter;
use Brotkrueml\JobRouterData\Domain\Demand\TableDemandFactory;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\TableNotFoundException;
use Brotkrueml\JobRouterData\Extension;
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
    ) {
    }

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
            $locale = $site->getLanguageById($record['sys_language_uid'])->getLocale();

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
