<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\SystemInformation;

use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
abstract class AbstractToolbarItemProvider
{
    protected $commandName = '';

    /** @var array|null */
    protected $lastRunInformation;

    public function __construct()
    {
        $this->lastRunInformation = GeneralUtility::makeInstance(Registry::class)
            ->get(Extension::REGISTRY_NAMESPACE, $this->commandName . '.lastRun');
    }

    public function getItem(SystemInformationToolbarItem $systemInformationToolbarItem): void
    {
        $systemInformationToolbarItem->addSystemInformation(
            $this->getLanguageService()->sL(
                \sprintf('%s:%s.lastRunLabel', Extension::LANGUAGE_PATH_TOOLBAR, $this->commandName)
            ),
            $this->getMessage(),
            'jobrouter-data-toolbar',
            $this->getSeverity()
        );
    }

    protected function getMessage(): string
    {
        $languageService = $this->getLanguageService();

        if ($this->lastRunInformation === null) {
            return $languageService->sL(
                \sprintf('%s:%s.neverRun', Extension::LANGUAGE_PATH_TOOLBAR, $this->commandName)
            );
        }

        if ($this->isWarning()) {
            $status = $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.warning');
        } elseif ($this->isOverdue()) {
            $status = $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.overdue');
        } else {
            $status = $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.success');
        }

        return \sprintf(
            $languageService->sL(
                \sprintf('%s:%s.lastRunMessage', Extension::LANGUAGE_PATH_TOOLBAR, $this->commandName)
            ),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $this->lastRunInformation['start']),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $this->lastRunInformation['start']),
            $status
        );
    }

    protected function isWarning(): bool
    {
        return $this->lastRunInformation['exitCode'] > 0;
    }

    protected function isOverdue(): bool
    {
        return $this->lastRunInformation['start'] < time() - 86400;
    }

    protected function getSeverity(): string
    {
        if ($this->lastRunInformation === null) {
            return InformationStatus::STATUS_WARNING;
        }

        if ($this->isWarning() || $this->isOverdue()) {
            $severity = InformationStatus::STATUS_WARNING;
        } else {
            $severity = InformationStatus::STATUS_OK;
        }

        return $severity;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
