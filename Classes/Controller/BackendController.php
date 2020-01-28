<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Controller;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

class BackendController extends ActionController
{
    private const MODULE_NAME = 'jobrouter_JobRouterDataJobrouterdata';

    protected $defaultViewObjectName = BackendTemplateView::class;

    /** @var TableRepository */
    private $tableRepository;

    /** @var IconFactory */
    private $iconFactory;

    /** @var UriBuilder */
    private $backendUriBuilder;

    /** @var ModuleTemplate */
    private $moduleTemplate;

    /** @var ButtonBar */
    private $buttonBar;

    public function injectTableRepository(TableRepository $tableRepository): void
    {
        $this->tableRepository = $tableRepository;
    }

    public function injectIconFactory(IconFactory $iconFactory): void
    {
        $this->iconFactory = $iconFactory;
    }

    public function injectUriBuilder(UriBuilder $uriBuilder): void
    {
        $this->backendUriBuilder = $uriBuilder;
    }

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        $this->moduleTemplate = $this->view->getModuleTemplate();

        $this->buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $this->createNewHeaderButton();
        $this->createReportHeaderButton();
        $this->createRefreshHeaderButton();
        $this->createShortcutHeaderButton();
    }

    public function listAction(): void
    {
        $pageRenderer = $this->moduleTemplate->getPageRenderer();
        $pageRenderer->addInlineLanguageLabelFile(
            'EXT:jobrouter_data/Resources/Private/Language/BackendModule.xlf'
        );
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/JobrouterData/TableCheck'
        );

        $simpleTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_SIMPLE);
        $ownTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_OWN_TABLE);
        $otherTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_OTHER_USAGE);

        $this->view->assignMultiple([
            'simpleTables' => $simpleTables,
            'ownTables' => $ownTables,
            'otherTables' => $otherTables,
        ]);
    }

    protected function createNewHeaderButton(): void
    {
        $title = $this->getLanguageService()->sL('LLL:EXT:jobrouter_data/Resources/Private/Language/BackendModule.xlf:action.add_table');

        $newRecordButton = $this->buttonBar->makeLinkButton()
            ->setHref((string)$this->backendUriBuilder->buildUriFromRoute(
                'record_edit',
                [
                    'edit' => [
                        'tx_jobrouterdata_domain_model_table' => ['new'],
                    ],
                    'returnUrl' => (string)$this->backendUriBuilder->buildUriFromRoute(self::MODULE_NAME),
                ]
            ))
            ->setTitle($title)
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));
        $this->buttonBar->addButton($newRecordButton, ButtonBar::BUTTON_POSITION_LEFT);
    }

    protected function createReportHeaderButton(): void
    {
        if (!ExtensionManagementUtility::isLoaded('reports')) {
            return;
        }

        $title = $this->getLanguageService()->sL('LLL:EXT:jobrouter_data/Resources/Private/Language/BackendModule.xlf:action.report');

        $reportButton = $this->buttonBar->makeLinkButton()
            ->setHref((string)$this->backendUriBuilder->buildUriFromRoute(
                'system_reports',
                [
                    'action' => 'detail',
                    'extension' => 'tx_jobrouterdata',
                    'report' => 'report',
                ]
            ))
            ->setTitle($title)
            ->setIcon($this->iconFactory->getIcon('jobrouterdata-action-report', Icon::SIZE_SMALL));
        $this->buttonBar->addButton($reportButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function createRefreshHeaderButton(): void
    {
        $title = $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload');

        $refreshButton = $this->buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($title)
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $this->buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function createShortcutHeaderButton(): void
    {
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $this->buttonBar->makeShortcutButton()
                ->setModuleName(self::MODULE_NAME)
                ->setGetVariables(['route', 'module', 'id'])
                ->setDisplayName('Shortcut');
            $this->buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
