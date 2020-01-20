<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Hooks;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Hook to display verbose information about pi plugin in Web>Page module
 */
final class PageLayoutView
{
    private const TEMPLATE = 'EXT:jobrouter_data/Resources/Private/Templates/PageLayout/Plugin.html';

    /** @var StandaloneView */
    private $view;

    /** @var TableRepository|object */
    private $tableRepository;

    private $flexformData;

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->view = $objectManager->get(StandaloneView::class);
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(static::TEMPLATE));

        $this->tableRepository = $objectManager->get(TableRepository::class);
    }

    public function getExtensionSummary(array $params): string
    {
        $this->flexformData = GeneralUtility::xml2array($params['row']['pi_flexform']);

        $tableId = (int)$this->getFieldFromFlexform('settings.table');
        $table = null;
        if ($tableId) {
            $table = $this->tableRepository->findByIdentifier($tableId);
        }

        $this->view->assign('table', $table);

        return $this->view->render();
    }

    public function getFieldFromFlexform(string $key, string $sheet = 'sDEF'): ?string
    {
        if (isset($this->flexformData['data'][$sheet]['lDEF'][$key]['vDEF'])) {
            return $this->flexformData['data'][$sheet]['lDEF'][$key]['vDEF'];
        }

        return null;
    }
}
