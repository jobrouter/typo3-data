<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository\QueryBuilder;

use Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder\TableRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TableRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private TableRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TableRepository($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function findAssignedCustomTablesReturnsEmptyArrayWhenNoTablesAreAvailable(): void
    {
        $actual = $this->subject->findAssignedCustomTables();

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function findAssignedCustomTablesWithAvailableCustomTables(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findAssignedCustomTables();

        self::assertCount(2, $actual);
        self::assertContains('tx_someext_domain_model_sometable', $actual);
        self::assertContains('tx_anothertable', $actual);
    }

    /**
     * @test
     */
    public function updateSynchronisationStatusForNonExistingTable(): void
    {
        $actual = $this->subject->updateSynchronisationStatus(9999, \time(), '', '');

        self::assertSame(0, $actual);
    }

    /**
     * @test
     */
    public function updateSynchronisationStatusForExistingTable(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->updateSynchronisationStatus(3, 1672473841, 'somehash', 'some error');

        self::assertSame(1, $actual);

        $row = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_table')
            ->select(
                ['last_sync_date', 'last_sync_error', 'datasets_sync_hash'],
                'tx_jobrouterdata_domain_model_table',
                [
                    'uid' => 3,
                ]
            )
            ->fetchAssociative();

        self::assertSame(1672473841, $row['last_sync_date']);
        self::assertSame('some error', $row['last_sync_error']);
        self::assertSame('somehash', $row['datasets_sync_hash']);
    }

    /**
     * @test
     */
    public function updateSynchronisationStatusHashIsUntouchedIfPassedAsEmptyString(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->updateSynchronisationStatus(3, 1672473842, '', '');

        self::assertSame(1, $actual);

        $row = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterdata_domain_model_table')
            ->select(
                ['last_sync_date', 'last_sync_error', 'datasets_sync_hash'],
                'tx_jobrouterdata_domain_model_table',
                [
                    'uid' => 3,
                ]
            )
            ->fetchAssociative();

        self::assertSame(1672473842, $row['last_sync_date']);
        self::assertSame('', $row['last_sync_error']);
        self::assertSame('ahash', $row['datasets_sync_hash']);
    }
}
