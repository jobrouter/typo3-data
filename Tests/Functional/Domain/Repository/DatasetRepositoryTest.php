<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Repository\DatasetRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DatasetRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private DatasetRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new DatasetRepository($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function findByTableUid(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_dataset.csv');

        $actual = $this->subject->findByTableUid(42);

        self::assertCount(3, $actual);
        self::assertSame(1, $actual[0]->uid);
        self::assertSame(2, $actual[1]->uid);
        self::assertSame(4, $actual[2]->uid);
    }

    /**
     * @test
     */
    public function deleteByTableUid(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_dataset.csv');

        $actual = $this->subject->deleteByTableUid(42);

        self::assertSame(3, $actual);

        $connection = $this->getConnectionPool()->getConnectionForTable('tx_jobrouterdata_domain_model_dataset');
        $uids = $connection->select(['uid'], 'tx_jobrouterdata_domain_model_dataset')->fetchFirstColumn();

        self::assertContains(3, $uids);
        self::assertContains(5, $uids);
    }
}
