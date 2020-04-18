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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class PreparerTest extends TestCase
{
    /** @var Preparer */
    private $subject;

    /** @var ObjectProphecy */
    private $persistenceManager;

    /** @var ObjectProphecy */
    private $transferRepository;

    protected function setUp(): void
    {
        $this->persistenceManager = $this->prophesize(PersistenceManager::class);
        $this->transferRepository = $this->prophesize(TransferRepository::class);

        $this->subject = new Preparer(
            $this->persistenceManager->reveal(),
            $this->transferRepository->reveal()
        );
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function storePersistsRecordCorrectly(): void
    {
        $transfer = new Transfer();
        $transfer->setPid(0);
        $transfer->setTableUid(42);
        $transfer->setIdentifier('some identifier');
        $transfer->setData('some data');

        $this->persistenceManager->persistAll()->shouldBeCalled();
        $this->transferRepository->add($transfer)->shouldBeCalled();

        $this->subject->store(42, 'some identifier', 'some data');
    }

    /**
     * @test
     */
    public function storeThrowsExceptionOnError(): void
    {
        $this->expectException(PrepareException::class);
        $this->expectExceptionCode(1579789397);

        $this->transferRepository->add(Argument::any())->willThrow(\Exception::class);

        $this->subject->store(42, 'some identifier', 'some data');
    }
}
