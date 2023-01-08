<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Enumerations\TableType;
use Brotkrueml\JobRouterData\Exception\TableNotFoundException;
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
    public function findAll(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findAll();

        self::assertCount(5, $actual);
        self::assertSame(1, $actual[0]->uid);
        self::assertSame(2, $actual[1]->uid);
        self::assertSame(3, $actual[2]->uid);
        self::assertSame(4, $actual[3]->uid);
        self::assertSame(5, $actual[4]->uid);
    }

    /**
     * @test
     */
    public function findAllByTypeWithHidden(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findAllByTypeWithHidden(TableType::Simple);

        self::assertCount(3, $actual);
        self::assertSame(1, $actual[0]->uid);
        self::assertSame(5, $actual[1]->uid);
        self::assertSame(6, $actual[2]->uid);
    }

    /**
     * @test
     */
    public function findByUidReturnsTableCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findByUid(3);

        self::assertSame(3, $actual->uid);
    }

    /**
     * @test
     */
    public function findByUidThrowsExceptionWhenTableIsDisabled(): void
    {
        $this->expectException(TableNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $this->subject->findByUid(6);
    }

    /**
     * @test
     */
    public function findByUidThrowsExceptionWhenTableIsDeleted(): void
    {
        $this->expectException(TableNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $this->subject->findByUid(7);
    }

    /**
     * @test
     */
    public function findByUidWithHiddenReturnsTableCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findByUidWithHidden(3);

        self::assertSame(3, $actual->uid);
    }

    /**
     * @test
     */
    public function findByUidWithHiddenReturnsDisabledTableCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findByUidWithHidden(6);

        self::assertSame(6, $actual->uid);
    }

    /**
     * @test
     */
    public function findByUidWithHiddenThrowsExceptionWhenTableIsDeleted(): void
    {
        $this->expectException(TableNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $this->subject->findByUidWithHidden(7);
    }

    /**
     * @test
     */
    public function findByHandleReturnsTableCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $actual = $this->subject->findByHandle('handle_2');

        self::assertSame(2, $actual->uid);
    }

    /**
     * @test
     */
    public function findByHandleThrowsExceptionWhenTableIsDisabled(): void
    {
        $this->expectException(TableNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $this->subject->findByHandle('handle_6');
    }

    /**
     * @test
     */
    public function findByHandleThrowsExceptionWhenTableIsDeleted(): void
    {
        $this->expectException(TableNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_jobrouterdata_domain_model_table.csv');

        $this->subject->findByHandle('handle_7');
    }
}
