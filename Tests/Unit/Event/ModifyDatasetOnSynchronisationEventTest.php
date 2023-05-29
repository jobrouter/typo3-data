<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Tests\Unit\Event;

use Brotkrueml\JobRouterData\Domain\Entity\Table;
use Brotkrueml\JobRouterData\Event\ModifyDatasetOnSynchronisationEvent;
use Brotkrueml\JobRouterData\Exception\ModifyDatasetException;
use Brotkrueml\JobRouterData\Tests\Helper\Entity\TableBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ModifyDatasetOnSynchronisationEventTest extends TestCase
{
    private Table $table;
    /**
     * @var array<string, int>|array<string, string>
     */
    private array $dataset;
    private ModifyDatasetOnSynchronisationEvent $subject;

    protected function setUp(): void
    {
        $this->table = (new TableBuilder())->build(1, 'some handle');

        $this->dataset = [
            'jrid' => 42,
            'field1' => 'content of field 1',
            'field2' => 'content of field 2',
        ];

        $this->subject = new ModifyDatasetOnSynchronisationEvent($this->table, $this->dataset);
    }

    #[Test]
    public function getTable(): void
    {
        self::assertSame($this->table, $this->subject->getTable());
    }

    #[Test]
    public function getDataset(): void
    {
        self::assertSame($this->dataset, $this->subject->getDataset());
    }

    #[Test]
    public function isRejectedIsFalseAfterInstantiationOfEvent(): void
    {
        self::assertFalse($this->subject->isRejected());
    }

    #[Test]
    public function isRejectedIsTrueAfterSetRejectedIsCalled(): void
    {
        $this->subject->setRejected();

        self::assertTrue($this->subject->isRejected());
    }

    #[Test]
    public function getAndSetDataset(): void
    {
        $dataset = [
            'jrid' => 42,
            'field1' => 'new content of field 1',
            'field2' => 'new content of field 2',
        ];
        $this->subject->setDataset($dataset);

        self::assertSame($dataset, $this->subject->getDataset());
    }

    #[Test]
    public function setDatasetThrowsExceptionWhenDatasetKeysAreRemoved(): void
    {
        $this->expectException(ModifyDatasetException::class);
        $this->expectExceptionCode(1639132693);
        $this->expectExceptionMessage('Given dataset keys "jrid, field1" differ from original dataset keys "jrid, field1, field2" when modfying dataset for table with handle "some handle"');

        $dataset = [
            'jrid' => 42,
            'field1' => 'new content of field 1',
        ];
        $this->subject->setDataset($dataset);
    }

    #[Test]
    public function setDatasetThrowsExceptionWhenJridIsChangedFromDataset(): void
    {
        $this->expectException(ModifyDatasetException::class);
        $this->expectExceptionCode(1639132877);
        $this->expectExceptionMessage('jrid must not be overriden for table with handle "some handle", original jrid is "42", new jrid id "21"');

        $dataset = [
            'jrid' => 21,
            'field1' => 'new content of field 1',
            'field2' => 'new content of field 2',
        ];
        $this->subject->setDataset($dataset);
    }
}
