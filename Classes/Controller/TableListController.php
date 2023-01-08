<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Controller;

use Brotkrueml\JobRouterData\Domain\Hydrator\TableRelationsHydrator;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Enumerations\TableType;
use Brotkrueml\JobRouterData\Extension;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class TableListController
{
    private ModuleTemplate $moduleTemplate;
    private StandaloneView $view;

    public function __construct(
        private readonly IconFactory $iconFactory,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly PageRenderer $pageRenderer,
        private readonly TableRelationsHydrator $tableRelationsHydrator,
        private readonly TableRepository $tableRepository,
        private readonly UriBuilder $uriBuilder,
    ) {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($request);

        $this->pageRenderer->addInlineLanguageLabelFile(
            \str_replace('LLL:', '', Extension::LANGUAGE_PATH_BACKEND_MODULE)
        );
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/JobrouterData/TableTest');

        $this->initializeView();
        $this->configureDocHeader($request->getAttribute('normalizedParams')?->getRequestUri() ?? '');
        $this->listAction();

        $this->moduleTemplate->setContent($this->view->render());

        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    private function initializeView(): void
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate('List');
        $this->view->setTemplateRootPaths(['EXT:' . Extension::KEY . '/Resources/Private/Templates/Backend']);
    }

    private function configureDocHeader(string $requestUri): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $newButton = $buttonBar->makeLinkButton()
            ->setHref((string)$this->uriBuilder->buildUriFromRoute(
                'record_edit',
                [
                    'edit' => [
                        'tx_jobrouterdata_domain_model_table' => ['new'],
                    ],
                    'returnUrl' => (string)$this->uriBuilder->buildUriFromRoute(Extension::MODULE_NAME),
                ]
            ))
            ->setTitle($this->getLanguageService()->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':action.add_table'))
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));
        $buttonBar->addButton($newButton);

        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref($requestUri)
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);

        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setRouteIdentifier('jobrouter_data')
                ->setDisplayName($this->getLanguageService()->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':heading_text'));
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    private function listAction(): void
    {
        $simpleTables = $this->tableRepository->findAllByTypeWithHidden(TableType::Simple);
        $customTables = $this->tableRepository->findAllByTypeWithHidden(TableType::CustomTable);
        $formFinisherTables = $this->tableRepository->findAllByTypeWithHidden(TableType::FormFinisher);
        $otherTables = $this->tableRepository->findAllByTypeWithHidden(TableType::OtherUsage);

        $this->view->assignMultiple([
            'simpleTables' => $this->tableRelationsHydrator->hydrateMultiple($simpleTables),
            'customTables' => $this->tableRelationsHydrator->hydrateMultiple($customTables),
            'formFinisherTables' => $this->tableRelationsHydrator->hydrateMultiple($formFinisherTables),
            'otherTables' => $this->tableRelationsHydrator->hydrateMultiple($otherTables),
        ]);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
