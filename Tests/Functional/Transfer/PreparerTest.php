<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Tests\Functional\Transfer;

use JobRouter\AddOn\Typo3Data\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Data\Exception\PrepareException;
use JobRouter\AddOn\Typo3Data\Transfer\Preparer;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\NullLogger;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class PreparerTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_base',
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    #[Test]
    public function storePersistsRecordCorrectly(): void
    {
        $subject = new Preparer(new NullLogger(), $this->getContainer()->get(TransferRepository::class));

        $subject->store(42, 'some correlation id', 'some data');

        $transfers = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_transfer')
            ->select(
                ['*'],
                'tx_jobrouterdata_domain_model_transfer',
            )
            ->fetchAllAssociative();

        self::assertCount(1, $transfers);
        self::assertSame(42, $transfers[0]['table_uid']);
        self::assertSame('some correlation id', $transfers[0]['correlation_id']);
        self::assertSame('some data', $transfers[0]['data']);
    }

    #[Test]
    public function storeThrowsExceptionOnError(): void
    {
        $this->expectException(PrepareException::class);
        $this->expectExceptionCode(1579789397);

        $transferRepositoryStub = $this->createStub(TransferRepository::class);
        $transferRepositoryStub
            ->method('add')
            ->willThrowException(new \Exception());

        $subject = new Preparer(new NullLogger(), $transferRepositoryStub);

        $subject->store(42, 'some correlation id', 'some data');
    }
}
