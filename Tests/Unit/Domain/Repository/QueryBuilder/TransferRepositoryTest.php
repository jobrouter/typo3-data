<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Repository\QueryBuilder;

use Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder\TransferRepository;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class TransferRepositoryTest extends TestCase
{
    /** @var Stub|QueryBuilder */
    private $queryBuilderStub;

    /** @var TransferRepository */
    private $subject;

    protected function setUp(): void
    {
        $this->queryBuilderStub = $this->createStub(QueryBuilder::class);

        $this->subject = new TransferRepository($this->queryBuilderStub);
    }

    /**
     * @test
     */
    public function deleteOldSuccessfulTransfersReturnsAffectedRowsCorrectly(): void
    {
        $this->queryBuilderStub
            ->method('delete')
            ->with('tx_jobrouterdata_domain_model_transfer')
            ->willReturn($this->queryBuilderStub);
        $this->queryBuilderStub
            ->method('where')
            ->willReturn($this->queryBuilderStub);
        $this->queryBuilderStub
            ->method('execute')
            ->willReturn(23);

        $actual = $this->subject->deleteOldSuccessfulTransfers(1234567890);

        self::assertSame(23, $actual);
    }
}
