<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Hooks;

use JobRouter\AddOn\Typo3Data\Domain\Repository\DatasetRepository;

/**
 * @internal
 */
final class TableUpdateHook
{
    public function __construct(
        private readonly DatasetRepository $datasetRepository,
    ) {}

    public function processCmdmap_postProcess(string $command, string $table, string|int $recordId): void
    {
        if ($table !== 'tx_jobrouterdata_domain_model_table') {
            return;
        }
        if ($command !== 'delete') {
            return;
        }

        $this->datasetRepository->deleteByTableUid((int)$recordId);
    }
}
