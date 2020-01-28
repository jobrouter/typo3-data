<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Report;

/*
 * This file is part of the "jobrouter_daza" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Reports\Controller\ReportController;
use TYPO3\CMS\Reports\ReportInterface;

class Status implements ReportInterface
{
    /** @var TableRepository */
    private $tableRepository;

    public function __construct(ReportController $reportController, TableRepository $tableRepository = null)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->tableRepository = $tableRepository ?? $objectManager->get(TableRepository::class);
    }

    public function getReport(): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:jobrouter_data/Resources/Private/Templates/Report/Status.html'
        ));

        $settings = [
            'dateTimeFormat' => \sprintf(
                '%s %s',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
            ),
        ];

        $lastSyncDate = 0;
        $syncTablesByType = [
            Table::TYPE_SIMPLE => [
                'totalActive' => 0,
                'erroneous' =>  [],
            ],
            Table::TYPE_OWN_TABLE => [
                'totalActive' => 0,
                'erroneous' =>  [],
            ],
        ];

        /** @var Table[] $activeTables */
        $activeTables = $this->tableRepository->findAllSyncTables();

        foreach ($activeTables as $table) {
            if ($lastSyncDate < $table->getLastSyncDate()) {
                $lastSyncDate = $table->getLastSyncDate();
            }

            $syncTablesByType[$table->getType()]['totalActive']++;

            if (!empty($table->getLastSyncError())) {
                $syncTablesByType[$table->getType()]['erroneous'][] = $table;
            }
        }

        $view->assignMultiple([
            'lastSyncDate' => $lastSyncDate,
            'syncTablesByType' => $syncTablesByType,
            'settings' => $settings,
        ]);

        return $view->render();
    }
}
