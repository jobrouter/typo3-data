<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Report;

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Reports\Controller\ReportController;
use TYPO3\CMS\Reports\ReportInterface;

final class Status implements ReportInterface
{
    private const TEMPLATE = 'EXT:jobrouter_data/Resources/Private/Templates/Report/Status.html';

    /** @var TableRepository */
    private $tableRepository;

    /** @var TransferRepository */
    private $transferRepository;

    /** @var StandaloneView */
    private $view;

    public function __construct(
        ReportController $reportController,
        TableRepository $tableRepository = null,
        TransferRepository $transferRepository = null,
        StandaloneView $view = null
    ) {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->tableRepository = $tableRepository ?? $objectManager->get(TableRepository::class);
        $this->transferRepository = $transferRepository ?? $objectManager->get(TransferRepository::class);

        $this->view = $view ?? $objectManager->get(StandaloneView::class);
        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(static::TEMPLATE));
    }

    public function getReport(): string
    {
        $this->assignSettingsToView();
        $this->assignSynchronisationDetailsToView();
        $this->assignTransferDetailsToView();

        return $this->view->render();
    }

    private function assignSettingsToView(): void
    {
        $settings = [
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ];

        $this->view->assign('settings', $settings);
    }

    private function assignSynchronisationDetailsToView(): void
    {
        $syncLastDate = 0;
        $syncTablesByType = [
            Table::TYPE_SIMPLE => [
                'totalActive' => 0,
                'erroneous' => [],
            ],
            Table::TYPE_OWN_TABLE => [
                'totalActive' => 0,
                'erroneous' => [],
            ],
        ];

        /** @var Table[] $activeTables */
        $activeTables = $this->tableRepository->findAllSyncTables();

        foreach ($activeTables as $table) {
            if ($syncLastDate < $table->getLastSyncDate()) {
                $syncLastDate = $table->getLastSyncDate();
            }

            $syncTablesByType[$table->getType()]['totalActive']++;

            if (!empty($table->getLastSyncError())) {
                $syncTablesByType[$table->getType()]['erroneous'][] = $table;
            }
        }

        $this->view->assignMultiple([
            'syncLastDate' => $syncLastDate,
            'syncTablesByType' => $syncTablesByType,
        ]);
    }

    private function assignTransferDetailsToView(): void
    {
        $total = $this->transferRepository->countAll();
        $successful = $this->transferRepository->countByTransmitSuccess(1);
        $erroneous = $this->transferRepository->countErroneousTransmissions();
        $inQueue = $total - $successful - $erroneous;

        $this->view->assignMultiple([
            'transferFirstTransmitDate' => $this->transferRepository->findFirstTransmitDate(),
            'transferLastTransmitDate' => $this->transferRepository->findLastTransmitDate(),
            'transferTotal' => $total,
            'transferTransmitSuccess' => $successful,
            'transferTransmitErroneous' => $erroneous,
            'transferInQueue' => $inQueue,
        ]);
    }
}
