<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Controller;

use JobRouter\AddOn\Typo3Data\Domain\Demand\TableDemandFactory;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Enumerations\TableType;
use JobRouter\AddOn\Typo3Data\Extension;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Page\PageRenderer;

#[AsController]
final class TableListController
{
    public function __construct(
        private readonly IconFactory $iconFactory,
        private readonly LanguageServiceFactory $languageServiceFactory,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly PageRenderer $pageRenderer,
        private readonly TableDemandFactory $tableDemandFactory,
        private readonly TableRepository $tableRepository,
        private readonly UriBuilder $uriBuilder,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);

        $this->pageRenderer->addInlineLanguageLabelFile(
            \str_replace('LLL:', '', Extension::LANGUAGE_PATH_BACKEND_MODULE),
        );
        $this->pageRenderer->addCssFile('EXT:' . Extension::KEY . '/Resources/Public/Css/styles.css');
        $this->pageRenderer->loadJavaScriptModule(
            '@jobrouter/data/connection-check.js',
        );

        $this->configureDocHeader($view, $request->getAttribute('normalizedParams')?->getRequestUri() ?? '');
        $this->listAction($view);

        return $view->renderResponse('Backend/List');
    }

    private function configureDocHeader(ModuleTemplate $view, string $requestUri): void
    {
        $languageService = $this->languageServiceFactory->createFromUserPreferences($this->getBackendUser());

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();

        $newButton = $buttonBar->makeLinkButton()
            ->setHref((string) $this->uriBuilder->buildUriFromRoute(
                'record_edit',
                [
                    'edit' => [
                        'tx_jobrouterdata_domain_model_table' => ['new'],
                    ],
                    'returnUrl' => (string) $this->uriBuilder->buildUriFromRoute(Extension::MODULE_NAME),
                ],
            ))
            ->setTitle($languageService->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':action.add_table'))
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));
        $buttonBar->addButton($newButton);

        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref($requestUri)
            ->setTitle($languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);

        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setRouteIdentifier('jobrouter_data')
                ->setDisplayName($languageService->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':heading_text'));
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    private function listAction(ModuleTemplate $view): void
    {
        $simpleTables = $this->tableRepository->findAllByTypeWithHidden(TableType::Simple);
        $customTables = $this->tableRepository->findAllByTypeWithHidden(TableType::CustomTable);
        $formFinisherTables = $this->tableRepository->findAllByTypeWithHidden(TableType::FormFinisher);
        $otherTables = $this->tableRepository->findAllByTypeWithHidden(TableType::OtherUsage);

        $view->assignMultiple([
            'simpleTables' => $this->tableDemandFactory->createMultiple($simpleTables),
            'customTables' => $this->tableDemandFactory->createMultiple($customTables),
            'formFinisherTables' => $this->tableDemandFactory->createMultiple($formFinisherTables),
            'otherTables' => $this->tableDemandFactory->createMultiple($otherTables),
        ]);
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
