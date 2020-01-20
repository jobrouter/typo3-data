<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterData\Domain\Model\Dataset;
use PHPUnit\Framework\TestCase;

class DatasetTest extends TestCase
{
    /** @var Dataset */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Dataset();
    }

    /**
     * @test
     */
    public function getAndSetTableUid(): void
    {
        $initial = $this->subject->getTableUid();
        self::assertSame(0, $initial);

        $this->subject->setTableUid(42);
        $actual = $this->subject->getTableUid();
        self::assertSame(42, $actual);
    }

    /**
     * @test
     */
    public function getAndSetJrid(): void
    {
        $initial = $this->subject->getJrid();
        self::assertSame(0, $initial);

        $this->subject->setJrid(111);
        $actual = $this->subject->getJrid();
        self::assertSame(111, $actual);
    }

    /**
     * @test
     */
    public function getAndSetDataset(): void
    {
        $initial = $this->subject->getDataset();
        self::assertSame('', $initial);

        $this->subject->setDataset('some dataset');
        $actual = $this->subject->getDataset();
        self::assertSame('some dataset', $actual);
    }

    /**
     * @test
     */
    public function getDatasetContentForColumnFromEmptyDataset(): void
    {
        $actual = $this->subject->getDatasetContentForColumn('someColumn');
        self::assertNull($actual);
    }

    /**
     * @test
     */
    public function getDatasetContentForColumnFromDataset(): void
    {
        $this->subject->setDataset(\json_encode(['someColumn' => 'someValue']));

        $actual = $this->subject->getDatasetContentForColumn('someColumn');
        self::assertSame('someValue', $actual);

        $actual = $this->subject->getDatasetContentForColumn('someOtherColumn');
        self::assertNull($actual);
    }

    /**
     * @test
     */
    public function getDatasetContentForColumnAfterSetDataset(): void
    {
        $this->subject->setDataset(\json_encode(['someColumn' => 'someValue']));
        $this->subject->setDataset(\json_encode(['someOtherColumn' => 'someOtherValue']));

        $actual = $this->subject->getDatasetContentForColumn('someColumn');
        self::assertNull($actual);

        $actual = $this->subject->getDatasetContentForColumn('someOtherColumn');
        self::assertSame('someOtherValue', $actual);
    }
}
