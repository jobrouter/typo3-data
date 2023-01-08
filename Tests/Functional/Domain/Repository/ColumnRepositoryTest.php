<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Repository\ColumnRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ColumnRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_data',
    ];

    private ColumnRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ColumnRepository($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function findByTableUidReturnsEmptyArrayIfNoRecordsAreFound(): void
    {
        $actual = $this->subject->findByTableUid(9999);

        self::assertIsArray($actual);
        self::assertCount(0, $actual);
    }

    /**
     * @test
     */
    public function findByTableUidReturnsAvailableColumns(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_column.csv');

        $actual = $this->subject->findByTableUid(42);

        self::assertCount(3, $actual);
        self::assertSame(4, $actual[0]->uid);
        self::assertSame(1, $actual[1]->uid);
        self::assertSame(2, $actual[2]->uid);
    }
}
