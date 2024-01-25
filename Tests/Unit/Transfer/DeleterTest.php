<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Unit\Transfer;

use JobRouter\AddOn\Typo3Data\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Data\Exception\DeleteException;
use JobRouter\AddOn\Typo3Data\Transfer\Deleter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class DeleterTest extends TestCase
{
    private Deleter $subject;
    private TransferRepository&Stub $transferRepositoryStub;

    protected function setUp(): void
    {
        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);
        $this->subject = new Deleter(new NullLogger(), $this->transferRepositoryStub);
    }

    #[Test]
    public function runReturnsTheAffectedRows(): void
    {
        $this->transferRepositoryStub
            ->method('deleteOldSuccessfulTransfers')
            ->with(self::anything())
            ->willReturn(42);

        self::assertSame(42, $this->subject->run(30));
    }

    #[Test]
    public function runThrowsAnExceptionWhenQueryFails(): void
    {
        $this->expectException(DeleteException::class);
        $this->expectExceptionCode(1582139672);
        $this->expectExceptionMessage('Error on clean up of old transfers: Some foo error');

        $this->transferRepositoryStub
            ->method('deleteOldSuccessfulTransfers')
            ->willThrowException(new \Exception('Some foo error'));

        $this->subject->run(30);
    }
}
