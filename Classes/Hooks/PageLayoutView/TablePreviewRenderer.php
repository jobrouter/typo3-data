<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Hooks\PageLayoutView;

use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class TablePreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
    private const TEMPLATE = 'EXT:' . Extension::KEY . '/Resources/Private/Templates/PageLayout/TablePreview.html';

    private $flexFormData;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var TableRepository
     */
    private $tableRepository;

    /**
     * @var LanguageService
     */
    private $languageService;

    public function __construct(TableRepository $tableRepository = null, StandaloneView $view = null, LanguageService $languageService = null)
    {
        $this->tableRepository = $tableRepository ?? GeneralUtility::makeInstance(TableRepository::class);
        $this->view = $view ?? GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(static::TEMPLATE));
        $this->languageService = $languageService ?? $GLOBALS['LANG'];
    }

    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        if ($row['CType'] !== 'tx_jobrouterdata_table') {
            return;
        }

        $headerContent = \sprintf(
            '<strong>%s</strong>',
            $this->languageService->sL(Extension::LANGUAGE_PATH_CONTENT_ELEMENT . ':ce.title')
        );

        $itemContent = $headerContent . $parentObject->linkEditContent($this->getItemContent($row), $row);

        $drawItem = false;
    }

    private function getItemContent(array $row): string
    {
        $this->flexFormData = GeneralUtility::xml2array($row['pi_flexform']);

        $tableId = (int)$this->getValueFromFlexform('table');
        $table = null;
        if ($tableId) {
            $table = $this->tableRepository->findByIdentifier($tableId);
        }

        $this->view->assign('table', $table);

        return $this->view->render();
    }

    private function getValueFromFlexform(string $key, string $sheet = 'sDEF'): ?string
    {
        return $this->flexFormData['data'][$sheet]['lDEF'][$key]['vDEF'] ?? null;
    }
}
