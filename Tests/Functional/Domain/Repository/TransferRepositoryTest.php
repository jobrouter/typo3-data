<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TransferRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_base',
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private TransferRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TransferRepository($this->getConnectionPool());
    }

    #[Test]
    public function findNotTransmitted(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->findNotTransmitted();

        self::assertCount(2, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(6, $actual[1]->uid);
    }

    #[Test]
    public function findErroneousTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->findErroneousTransfers();

        self::assertCount(1, $actual);
        self::assertSame(2, $actual[0]->uid);
    }

    #[Test]
    public function add(): void
    {
        $date = new \DateTimeImmutable();
        $actual = $this->subject->add(1, 'some correlation', 'some data', $date);

        self::assertSame(1, $actual);

        $rows = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_transfer')
            ->select(
                ['*'],
                'tx_jobrouterdata_domain_model_transfer',
            )->fetchAllAssociative();

        self::assertCount(1, $rows);
        self::assertSame($date->getTimestamp(), $rows[0]['crdate']);
        self::assertSame(1, $rows[0]['table_uid']);
        self::assertSame('some correlation', $rows[0]['correlation_id']);
        self::assertSame('some data', $rows[0]['data']);
    }

    #[Test]
    public function updateTransmitData(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $date = new \DateTimeImmutable();
        $actual = $this->subject->updateTransmitData(6, true, $date, 'some message');

        self::assertSame(1, $actual);

        $row = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_transfer')
            ->select(
                ['*'],
                'tx_jobrouterdata_domain_model_transfer',
                [
                    'uid' => 6,
                ],
            )->fetchAssociative();

        self::assertSame(1, $row['transmit_success']);
        self::assertSame($date->getTimestamp(), $row['transmit_date']);
        self::assertSame('some message', $row['transmit_message']);
    }

    #[Test]
    public function countGroupByTransmitSuccessWithValuesForBoth(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->countGroupByTransmitSuccess();

        self::assertCount(2, $actual);
        self::assertSame(0, $actual[0]['transmit_success']);
        self::assertSame(2, $actual[0]['count']);
        self::assertSame(1, $actual[1]['transmit_success']);
        self::assertSame(4, $actual[1]['count']);
    }

    #[Test]
    public function countGroupByTransmitSuccessWithNoEntriesAvailable(): void
    {
        $actual = $this->subject->countGroupByTransmitSuccess();

        self::assertCount(0, $actual);
    }

    #[Test]
    public function countTransmitFailedWithAvailableFailedTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->countTransmitFailed();

        self::assertSame(1, $actual);
    }

    #[Test]
    public function countTransmitFailedWithNoAvailableFailedTransfers(): void
    {
        $actual = $this->subject->countTransmitFailed();

        self::assertSame(0, $actual);
    }

    #[Test]
    public function deleteOldSuccessfulTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->deleteOldSuccessfulTransfers(1600000003);

        self::assertSame(2, $actual);

        $rows = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_transfer')
            ->select(
                ['uid'],
                'tx_jobrouterdata_domain_model_transfer',
            )
            ->fetchAllAssociative();

        $availableUids = \array_map(static fn(array $row): int => (int)$row['uid'], $rows);

        self::assertNotContains(1, $availableUids);
        self::assertNotContains(3, $availableUids);
    }

    #[Test]
    public function findFirstCreationDateWithAvailableTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_transfer.csv');

        $actual = $this->subject->findFirstCreationDate();

        self::assertSame(1600000000, $actual);
    }

    #[Test]
    public function findFirstCreationDateWithNoAvailableTransfers(): void
    {
        $actual = $this->subject->findFirstCreationDate();

        self::assertSame(0, $actual);
    }
}
