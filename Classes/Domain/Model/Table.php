<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Domain\Model;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use Brotkrueml\JobRouterData\Domain\Model\Table\Cell;
use Brotkrueml\JobRouterData\Domain\Model\Table\Row;
use Brotkrueml\JobRouterData\Enumeration\ColumnTypeEnumeration;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Table model
 */
class Table extends AbstractEntity
{
    public const TYPE_SIMPLE = 1;
    public const TYPE_LOCAL_TABLE = 2;

    /** @var int */
    protected $type = 0;

    /** @var string */
    protected $name = '';

    /** @var \Brotkrueml\JobRouterConnector\Domain\Model\Connection|null */
    protected $connection = null;

    /** @var string */
    protected $tableGuid = '';

    /** @var string */
    protected $localTable = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Column>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $columns = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Dataset>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $datasets = null;

    /** @var bool */
    protected $disabled = false;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->columns = new ObjectStorage();
        $this->datasets = new ObjectStorage();
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    public function getTableGuid(): string
    {
        return $this->tableGuid;
    }

    public function setTableGuid(string $tableGuid): void
    {
        $this->tableGuid = $tableGuid;
    }

    public function getLocalTable(): string
    {
        return $this->localTable;
    }

    public function setLocalTable(string $localTable): void
    {
        $this->localTable = $localTable;
    }

    public function addColumn(Column $column): void
    {
        $this->columns->attach($column);
    }

    public function removeColumn(Column $columnToRemove): void
    {
        $this->columns->detach($columnToRemove);
    }

    public function getColumns(): ObjectStorage
    {
        return $this->columns;
    }

    public function setColumns(ObjectStorage $columns)
    {
        $this->columns = $columns;
    }

    public function getCountRows(): int
    {
        return count($this->datasets);
    }

    public function getRows(): array
    {
        $rows = [];
        foreach ($this->datasets as $dataset) {
            $datasetArray = \json_decode($dataset->getDataset(), true);

            $row = new Row();
            foreach ($this->columns as $column) {
                $cell = new Cell();
                $cell->setName($column->getName());

                if ($column->getName() === 'jrid') {
                    $cell->setContent($dataset->getJrid());
                    $cell->setType(ColumnTypeEnumeration::INTEGER);
                } else {
                    $cell->setContent($datasetArray[$column->getName() ?? '']);
                    $cell->setType($column->getType());
                    $cell->setDecimalPlaces($column->getDecimalPlaces());
                }

                $row->addCell($cell);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
