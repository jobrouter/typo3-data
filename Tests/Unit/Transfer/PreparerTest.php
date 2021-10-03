<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Transfer;

use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Exception\PrepareException;
use Brotkrueml\JobRouterData\Transfer\Preparer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class PreparerTest extends TestCase
{
    /**
     * @var Preparer
     */
    private $subject;

    /**
     * @var MockObject|PersistenceManager
     */
    private $persistenceManagerMock;

    /**
     * @var MockObject|TransferRepository
     */
    private $transferRepositoryMock;

    protected function setUp(): void
    {
        $this->persistenceManagerMock = $this->createMock(PersistenceManager::class);
        $this->transferRepositoryMock = $this->createMock(TransferRepository::class);

        $this->subject = new Preparer($this->persistenceManagerMock, $this->transferRepositoryMock);
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function storePersistsRecordCorrectly(): void
    {
        $transfer = new Transfer();
        $transfer->setCrdate(\time());
        $transfer->setPid(0);
        $transfer->setTableUid(42);
        $transfer->setCorrelationId('some correlation id');
        $transfer->setData('some data');

        $this->persistenceManagerMock
            ->expects(self::once())
            ->method('persistAll');

        $this->transferRepositoryMock
            ->expects(self::once())
            ->method('add')
            ->with($transfer);

        $this->subject->store(42, 'some correlation id', 'some data');
    }

    /**
     * @test
     */
    public function storeThrowsExceptionOnError(): void
    {
        $this->expectException(PrepareException::class);
        $this->expectExceptionCode(1579789397);

        $this->transferRepositoryMock
            ->method('add')
            ->willThrowException(new \Exception());

        $this->subject->store(42, 'some correlation id', 'some data');
    }
}
