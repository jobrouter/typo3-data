<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Dataset;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Model\Table\Row;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class TableTest extends TestCase
{
    /** @var Table */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Table();
    }

    /**
     * @test
     */
    public function getAndSetType(): void
    {
        self::assertSame(0, $this->subject->getType());

        $this->subject->setType(2);

        self::assertSame(2, $this->subject->getType());
    }

    /**
     * @test
     */
    public function getAndSetName(): void
    {
        self::assertSame('', $this->subject->getName());

        $this->subject->setName('some name');

        self::assertSame('some name', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getAndSetConnection(): void
    {
        self::assertNull($this->subject->getConnection());

        $connection = new Connection();
        $this->subject->setConnection($connection);

        self::assertSame($connection, $this->subject->getConnection());
    }

    /**
     * @test
     */
    public function getAndSetTableGuid(): void
    {
        self::assertSame('', $this->subject->getTableGuid());

        $this->subject->setTableGuid('some table guid');

        self::assertSame('some table guid', $this->subject->getTableGuid());
    }

    /**
     * @test
     */
    public function getAndSetOwnTable(): void
    {
        self::assertSame('', $this->subject->getOwnTable());

        $this->subject->setOwnTable('some own table');

        self::assertSame('some own table', $this->subject->getOwnTable());
    }

    /**
     * @test
     */
    public function getColumnsInitiallyIsAnEmptyObjectStorage(): void
    {
        self::assertInstanceOf(ObjectStorage::class, $this->subject->getColumns());
        self::assertSame(0, $this->subject->getColumns()->count());
    }

    /**
     * @test
     */
    public function addRemoveAndGetColumns(): void
    {
        $column1 = new Column();
        $column1->setName('column 1');

        $column2 = new Column();
        $column2->setName('column 2');

        $this->subject->addColumn($column1);
        self::assertSame(1, $this->subject->getColumns()->count());

        $this->subject->addColumn($column2);
        self::assertSame(2, $this->subject->getColumns()->count());

        self::assertTrue($this->subject->getColumns()->contains($column1));
        self::assertTrue($this->subject->getColumns()->contains($column2));

        $this->subject->removeColumn($column1);
        self::assertSame(1, $this->subject->getColumns()->count());

        self::assertFalse($this->subject->getColumns()->contains($column1));
    }

    /**
     * @test
     */
    public function setColumns(): void
    {
        $column = new Column();
        $column->setName('column');

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($column);

        $this->subject->setColumns($objectStorage);

        self::assertSame($objectStorage, $this->subject->getColumns());
    }

    /**
     * @test
     */
    public function getDatasetsInitiallyIsAnEmptyObjectStorage(): void
    {
        self::assertInstanceOf(ObjectStorage::class, $this->subject->getDatasets());
        self::assertSame(0, $this->subject->getDatasets()->count());
    }

    /**
     * @test
     */
    public function addRemoveAndGetDatasets(): void
    {
        $dataset1 = new Dataset();
        $dataset2 = new Dataset();

        $this->subject->addDataset($dataset1);
        self::assertSame(1, $this->subject->getDatasets()->count());

        $this->subject->addDataset($dataset2);
        self::assertSame(2, $this->subject->getDatasets()->count());

        self::assertTrue($this->subject->getDatasets()->contains($dataset1));
        self::assertTrue($this->subject->getDatasets()->contains($dataset2));

        $this->subject->removeDataset($dataset1);
        self::assertSame(1, $this->subject->getDatasets()->count());

        self::assertFalse($this->subject->getDatasets()->contains($dataset1));
    }

    /**
     * @test
     */
    public function setDatasets(): void
    {
        $dataset = new Column();

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($dataset);

        $this->subject->setDatasets($objectStorage);

        self::assertSame($objectStorage, $this->subject->getDatasets());
    }

    /**
     * @test
     */
    public function getCountRows(): void
    {
        $dataset1 = new Dataset();
        $this->subject->addDataset($dataset1);
        $dataset2 = new Dataset();
        $this->subject->addDataset($dataset2);

        self::assertSame(2, $this->subject->getCountRows());
    }

    /**
     * @test
     */
    public function getRows(): void
    {
        $column1 = new Column();
        $column1->setName('column1');
        $this->subject->addColumn($column1);
        $column2 = new Column();
        $column2->setName('column2');
        $this->subject->addColumn($column2);
        $column3 = new Column();
        $column3->setName('jrid');
        $this->subject->addColumn($column3);

        $dataset1 = new Dataset();
        $dataset1->setDataset(\json_encode(['jrid' => 1, 'column1' => 'value1-1', 'column2' => 'value1-2']));
        $this->subject->addDataset($dataset1);
        $dataset2 = new Dataset();
        $dataset2->setDataset(\json_encode(['jrid' => 2, 'column1' => 'value2-1', 'column2' => 'value2-2']));
        $this->subject->addDataset($dataset2);

        /** @var Row[] $actual */
        $actual = $this->subject->getRows();

        self::assertCount(2, $actual);
        self::assertCount(3, $actual[0]->getCells());
        self::assertCount(3, $actual[1]->getCells());
    }

    /**
     * @test
     */
    public function isAndSetDisabled(): void
    {
        self::assertFalse($this->subject->isDisabled());

        $this->subject->setDisabled(true);

        self::assertTrue($this->subject->isDisabled());
    }

    /**
     * @test
     */
    public function getAndSetDatasetsSyncHash(): void
    {
        self::assertSame('', $this->subject->getDatasetsSyncHash());

        $this->subject->setDatasetsSyncHash('some datasets sync hash');

        self::assertSame('some datasets sync hash', $this->subject->getDatasetsSyncHash());
    }

    /**
     * @test
     */
    public function getAndSetLastSyncDate(): void
    {
        self::assertNull($this->subject->getLastSyncDate());

        $date = new \DateTime();
        $this->subject->setLastSyncDate($date);

        self::assertInstanceOf(\DateTime::class, $this->subject->getLastSyncDate());
        self::assertSame($date, $this->subject->getLastSyncDate());
    }

    /**
     * @test
     */
    public function getAndSetLastSyncError(): void
    {
        self::assertSame('', $this->subject->getLastSyncError());

        $this->subject->setLastSyncError('some last sync error');

        self::assertSame('some last sync error', $this->subject->getLastSyncError());
    }
}
