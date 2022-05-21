<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Transfer;

use Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterData\Exception\DeleteException;
use Brotkrueml\JobRouterData\Transfer\Deleter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class DeleterTest extends TestCase
{
    private Deleter $subject;

    /**
     * @var Stub&TransferRepository
     */
    private $transferRepositoryStub;

    protected function setUp(): void
    {
        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);
        $this->subject = new Deleter($this->transferRepositoryStub);
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function runReturnsTheAffectedRows(): void
    {
        $this->transferRepositoryStub
            ->method('deleteOldSuccessfulTransfers')
            ->with(self::anything())
            ->willReturn(42);

        self::assertSame(42, $this->subject->run(30));
    }

    /**
     * @test
     */
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
