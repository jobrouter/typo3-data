<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository\QueryBuilder;

use Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder\TransferRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TransferRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private TransferRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TransferRepository($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function countGroupByTransmitSuccessWithValuesForBoth(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->countGroupByTransmitSuccess();

        self::assertCount(2, $actual);
        self::assertSame(0, $actual[0]['transmit_success']);
        self::assertSame(2, $actual[0]['count']);
        self::assertSame(1, $actual[1]['transmit_success']);
        self::assertSame(4, $actual[1]['count']);
    }

    /**
     * @test
     */
    public function countGroupByTransmitSuccessWithNoEntriesAvailable(): void
    {
        $actual = $this->subject->countGroupByTransmitSuccess();

        self::assertCount(0, $actual);
    }

    /**
     * @test
     */
    public function countTransmitFailedWithAvailableFailedTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->countTransmitFailed();

        self::assertSame(1, $actual);
    }

    /**
     * @test
     */
    public function countTransmitFailedWithNoAvailableFailedTransfers(): void
    {
        $actual = $this->subject->countTransmitFailed();

        self::assertSame(0, $actual);
    }

    /**
     * @test
     */
    public function deleteOldSuccessfulTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->deleteOldSuccessfulTransfers(1600000003);

        self::assertSame(2, $actual);

        $rows = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_transfer')
            ->select(
                ['uid'],
                'tx_jobrouterdata_domain_model_transfer'
            )
            ->fetchAllAssociative();

        $availableUids = \array_map(static fn (array $row): int => (int)$row['uid'], $rows);

        self::assertNotContains(1, $availableUids);
        self::assertNotContains(3, $availableUids);
    }

    /**
     * @test
     */
    public function findFirstCreationDateWithAvailableTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->findFirstCreationDate();

        self::assertSame(1600000000, $actual);
    }

    /**
     * @test
     */
    public function findFirstCreationDateWithNoAvailableTransfers(): void
    {
        $actual = $this->subject->findFirstCreationDate();

        self::assertSame(0, $actual);
    }
}
