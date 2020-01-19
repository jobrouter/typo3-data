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

    /** @var ModuleTemplate */
    private $moduleTemplate;

    public function injectTableRepository(TableRepository $tableRepository)
    {
        $this->tableRepository = $tableRepository;
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

        $this->createDocumentHeaderButtons();
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
        $localTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_OWN_TABLE);

        if (ExtensionManagementUtility::isLoaded('jobrouter_form')) {
            $otherTables = $this->tableRepository->findAllByTypeWithHidden(Table::TYPE_OTHER_USAGE);
        } else {
            $otherTables = [];
        }

        $this->view->assignMultiple([
            'simpleTables' => $simpleTables,
            'localTables' => $localTables,
            'otherTables' => $otherTables,
        ]);
    }

    protected function createDocumentHeaderButtons(): void
    {
        /** @var ButtonBar $buttonBar */
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $title = $this->getLanguageService()->sL('LLL:EXT:jobrouter_data/Resources/Private/Language/BackendModule.xlf:action.add_table');

        $newRecordButton = $buttonBar->makeLinkButton()
            ->setHref((string)$uriBuilder->buildUriFromRoute(
                'record_edit',
                [
                    'edit' => [
                        'tx_jobrouterdata_domain_model_table' => ['new'],
                    ],
                    'returnUrl' => (string)$uriBuilder->buildUriFromRoute(self::MODULE_NAME),
                ]
            ))
            ->setTitle($title)
            ->setIcon($iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));

        $buttonBar->addButton($newRecordButton, ButtonBar::BUTTON_POSITION_LEFT);

        $refreshButton = $buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);

        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName(self::MODULE_NAME)
                ->setGetVariables(['route', 'module', 'id'])
                ->setDisplayName('Shortcut');
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
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
