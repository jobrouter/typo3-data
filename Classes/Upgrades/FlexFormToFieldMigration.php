<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Upgrades;

use JobRouter\AddOn\Typo3Data\Domain\Repository\ContentRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Migrate "table" field from FlexForms to dedicated table field in tt_content table.
 *
 * @since 5.0.0
 * @internal
 */
#[UpgradeWizard('jobrouter-data/flexform-to-field-migration')]
final readonly class FlexFormToFieldMigration implements UpgradeWizardInterface
{
    public function __construct(
        private ContentRepository $contentRepository,
        private FlexFormService $flexFormService,
    ) {}

    public function getTitle(): string
    {
        return 'Migrate JobData FlexForm field "table" to native database field';
    }

    public function getDescription(): string
    {
        return '';
    }

    public function executeUpdate(): bool
    {
        foreach ($this->contentRepository->findByFlexFormField() as $content) {
            $flexForm = $this->flexFormService->convertFlexFormContentToArray($content->flexForm);
            $tableId = (int) ($flexForm['table'] ?? 0);
            if ($tableId <= 0) {
                continue;
            }

            $this->contentRepository->updateTableFieldAndResetFlexFormField((int) $content->uid, $tableId);
        }

        return true;
    }

    public function updateNecessary(): bool
    {
        return $this->contentRepository->findByFlexFormField() !== [];
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}
