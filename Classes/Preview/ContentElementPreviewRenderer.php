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
use JobRouter\AddOn\Typo3Data\Extension;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

#[AsEventListener(
    identifier: 'jobrouter-data/content-element-preview-renderer',
)]
final readonly class ContentElementPreviewRenderer
{
    public function __construct(
        private DatasetConverter $datasetConverter,
        private SiteFinder $siteFinder,
        private TableDemandFactory $tableDemandFactory,
        private TableRepository $tableRepository,
        private ViewFactoryInterface $viewFactory,
    ) {}

    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        if ($event->getTable() !== 'tt_content') {
            return;
        }

        if ($event->getRecordType() !== 'tx_jobrouterdata_table') {
            return;
        }

        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: ['EXT:' . Extension::KEY . '/Resources/Private/Templates/Preview/'],
            request: $event->getPageLayoutContext()->getCurrentRequest(),
        );
        $view = $this->viewFactory->create($viewFactoryData);

        // @todo Migrate to RecordInterface completely when compatibility with TYPO3 v13 is removed
        $record = $event->getRecord();
        // @phpstan-ignore-next-line Instanceof between TYPO3\CMS\Core\Domain\RecordInterface and TYPO3\CMS\Core\Domain\RecordInterface will always evaluate to true.
        if ($record instanceof RecordInterface) {
            $record = $record->toArray();
        }
        $tableId = (int) ($record['tx_jobrouterdata_table'] ?? 0);

        try {
            $table = $this->tableRepository->findByUid($tableId);
            $site = $this->siteFinder->getSiteByPageId($record['pid']);
            $locale = $site->getLanguageById($record['sys_language_uid'])->getLocale()->getName();

            $view->assignMultiple([
                'record' => $record,
                'tableDemand' => $this->tableDemandFactory->create($table),
                'rows' => $this->datasetConverter->convertFromJsonToArray($table, $locale),
            ]);
        } catch (\Exception) {
        }

        $event->setPreviewContent($view->render('ContentElement'));
    }
}
