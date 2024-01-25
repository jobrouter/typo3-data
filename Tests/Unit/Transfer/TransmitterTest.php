<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Transfer;

use JobRouter\AddOn\Typo3Data\Domain\Repository\JobRouter\JobDataRepository;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Data\Transfer\Transmitter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class TransmitterTest extends TestCase
{
    private JobDataRepository&Stub $jobDataRepositoryStub;
    private TransferRepository&Stub $transferRepositoryStub;
    private TableRepository&Stub $tableRepositoryStub;
    private Transmitter $subject;

    protected function setUp(): void
    {
        $this->jobDataRepositoryStub = $this->createStub(JobDataRepository::class);
        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);
        $this->tableRepositoryStub = $this->createStub(TableRepository::class);

        $this->subject = new Transmitter(
            $this->jobDataRepositoryStub,
            new NullLogger(),
            $this->transferRepositoryStub,
            $this->tableRepositoryStub,
        );
    }

    #[Test]
    public function transmitWithNoTransfersAvailableReturns0TotalsAndErrors(): void
    {
        $this->transferRepositoryStub
            ->method('findNotTransmitted')
            ->willReturn([]);

        $actual = $this->subject->run();

        self::assertSame(0, $actual->total);
        self::assertSame(0, $actual->errors);
    }
}
