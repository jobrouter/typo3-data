<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Hooks\PageLayoutView;

use Brotkrueml\JobRouterData\Domain\Converter\DatasetConverter;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class JobDataTablePreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
    private const TEMPLATE = 'EXT:' . Extension::KEY . '/Resources/Private/Templates/PageLayout/JobDataTablePreview.html';

    /**
     * @var mixed|null
     */
    private $flexFormData;
    private readonly StandaloneView $view;
    private readonly DatasetConverter $datasetConverter;
    private readonly SiteFinder $siteFinder;
    private readonly TableRepository $tableRepository;
    private readonly LanguageService $languageService;

    public function __construct()
    {
        $this->datasetConverter = GeneralUtility::makeInstance(DatasetConverter::class);
        $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $this->tableRepository = GeneralUtility::makeInstance(TableRepository::class);
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(self::TEMPLATE));
        $this->languageService = $GLOBALS['LANG'];
    }

    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row): void
    {
        if ($row['CType'] !== 'tx_jobrouterdata_table') {
            return;
        }

        $headerContent = \sprintf(
            '<strong>%s</strong>',
            $this->languageService->sL(Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':ce.title')
        );

        $itemContent = $parentObject->linkEditContent($this->getItemContent($row), $row);

        $drawItem = false;
    }

    private function getItemContent(array $row): string
    {
        $this->flexFormData = GeneralUtility::xml2array($row['pi_flexform']);

        $tableId = (int)$this->getValueFromFlexform('table');
        /** @var Table|null $table */
        $table = $this->tableRepository->findByIdentifier($tableId);

        $site = $this->siteFinder->getSiteByPageId($row['pid']);
        $locale = $site->getLanguageById($row['sys_language_uid'])->getLocale();

        $this->view->assign('table', $table);
        if ($table instanceof Table) {
            $this->view->assign('rows', $this->datasetConverter->convertFromJsonToArray($table, $locale));
        }

        return $this->view->render();
    }

    private function getValueFromFlexform(string $key, string $sheet = 'sDEF'): ?string
    {
        return $this->flexFormData['data'][$sheet]['lDEF'][$key]['vDEF'] ?? null;
    }
}
