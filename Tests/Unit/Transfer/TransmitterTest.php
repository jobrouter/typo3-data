<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Transfer;

use Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Transfer\Transmitter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class TransmitterTest extends TestCase
{
    private Stub&JobDataRepository $jobDataRepositoryStub;
    private Stub&TransferRepository $transferRepositoryStub;
    private Stub&TableRepository $tableRepositoryStub;
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

    /**
     * @test
     */
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
