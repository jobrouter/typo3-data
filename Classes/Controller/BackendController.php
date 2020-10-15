<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Controller;

use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * @internal
 */
final class BackendController extends ActionController
{
    private const MODULE_NAME = 'jobrouter_JobRouterDataJobrouterdata';

    protected $defaultViewObjectName = BackendTemplateView::class;

    /** @var IconFactory */
    private $iconFactory;

    /** @var LanguageService */
    private $languageService;

    /** @var TableRepository */
    private $tableRepository;

    /** @var UriBuilder */
    private $backendUriBuilder;

    /** @var ModuleTemplate */
    private $moduleTemplate;

    /** @var ButtonBar */
    private $buttonBar;

    public function __construct(
        IconFactory $iconFactory,
        LanguageService $languageService,
        TableRepository $tableRepository,
        UriBuilder $uriBuilder
    ) {
        $this->iconFactory = $iconFactory;
        $this->languageService = $languageService;
        $this->tableRepository = $tableRepository;
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
        $this->createRefreshHeaderButton();
        $this->createShortcutHeaderButton();
    }

    public function listAction(): void
    {
        $pageRenderer = $this->moduleTemplate->getPageRenderer();
        $pageRenderer->addInlineLanguageLabelFile(
            \str_replace('LLL:', '', Extension::LANGUAGE_PATH_BACKEND_MODULE)
        );
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/JobrouterData/TableCheck'
        );

        $simpleTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_SIMPLE);
        $ownTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_OWN_TABLE);
        $formFinisherTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_FORM_FINISHER);
        $otherTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_OTHER_USAGE);

        $this->view->assignMultiple([
            'simpleTables' => $simpleTables,
            'ownTables' => $ownTables,
            'formFinisherTables' => $formFinisherTables,
            'otherTables' => $otherTables,
        ]);
    }

    protected function createNewHeaderButton(): void
    {
        $title = $this->languageService->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':action.add_table');

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

    protected function createRefreshHeaderButton(): void
    {
        $title = $this->languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload');

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

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
