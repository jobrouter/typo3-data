<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Hooks;

use Brotkrueml\JobRouterData\Domain\Repository\DatasetRepository;
use Brotkrueml\JobRouterData\Hooks\TableUpdateHook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TableUpdateHookTest extends TestCase
{
    private DatasetRepository&MockObject $datasetRepository;
    private TableUpdateHook $subject;

    protected function setUp(): void
    {
        $this->datasetRepository = $this->createMock(DatasetRepository::class);

        $this->subject = new TableUpdateHook($this->datasetRepository);
    }

    #[Test]
    public function tableIsDeletedRemovesDatasets(): void
    {
        $this->datasetRepository
            ->expects(self::once())
            ->method('deleteByTableUid')
            ->with(42);

        $this->subject->processCmdmap_postProcess(
            'delete',
            'tx_jobrouterdata_domain_model_table',
            42,
        );
    }

    #[Test]
    public function otherTableIsProcessedDoesNothing(): void
    {
        $this->datasetRepository
            ->expects(self::never())
            ->method('deleteByTableUid');

        $this->subject->processCmdmap_postProcess(
            'delete',
            'some_other_table',
            42,
        );
    }

    #[Test]
    public function otherActionThanDeleteDoesNothing(): void
    {
        $this->datasetRepository
            ->expects(self::never())
            ->method('deleteByTableUid');

        $this->subject->processCmdmap_postProcess(
            'copy',
            'tx_jobrouterdata_domain_model_table',
            42,
        );
    }
}
