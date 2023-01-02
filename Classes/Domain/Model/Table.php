<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Model;

use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use TYPO3\CMS\Extbase\Annotation\ORM as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Table model
 */
class Table extends AbstractEntity
{
    protected int $type = 0;
    protected string $handle = '';
    protected string $name = '';
    protected ?Connection $connection = null;
    protected string $tableGuid = '';
    protected string $customTable = '';
    /**
     * @var ObjectStorage<Column>
     * @Extbase\Cascade
     * @Extbase\Lazy
     */
    protected $columns;

    /**
     * @var ObjectStorage<Dataset>
     * @Extbase\Cascade
     * @Extbase\Lazy
     */
    protected $datasets;

    protected string $datasetsSyncHash = '';
    protected bool $disabled = false;
    protected ?\DateTime $lastSyncDate = null;
    protected string $lastSyncError = '';

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

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
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

    public function getCustomTable(): string
    {
        return $this->customTable;
    }

    public function setCustomTable(string $customTable): void
    {
        $this->customTable = $customTable;
    }

    public function addColumn(Column $column): void
    {
        $this->columns->attach($column);
    }

    public function removeColumn(Column $columnToRemove): void
    {
        $this->columns->detach($columnToRemove);
    }

    /**
     * @return ObjectStorage<Column>
     */
    public function getColumns(): ObjectStorage
    {
        return $this->columns;
    }

    /**
     * @param ObjectStorage<Column> $columns
     */
    public function setColumns(ObjectStorage $columns): void
    {
        $this->columns = $columns;
    }

    public function addDataset(Dataset $dataset): void
    {
        $this->datasets->attach($dataset);
    }

    public function removeDataset(Dataset $datasetToRemove): void
    {
        $this->datasets->detach($datasetToRemove);
    }

    /**
     * @return ObjectStorage<Dataset>
     */
    public function getDatasets(): ObjectStorage
    {
        return $this->datasets;
    }

    /**
     * @param ObjectStorage<Dataset> $datasets
     */
    public function setDatasets(ObjectStorage $datasets): void
    {
        $this->datasets = $datasets;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    public function getDatasetsSyncHash(): string
    {
        return $this->datasetsSyncHash;
    }

    public function setDatasetsSyncHash(string $datasetsSyncHash): void
    {
        $this->datasetsSyncHash = $datasetsSyncHash;
    }

    public function getLastSyncDate(): ?\DateTime
    {
        return $this->lastSyncDate;
    }

    public function setLastSyncDate(\DateTime $lastSyncDate): void
    {
        $this->lastSyncDate = $lastSyncDate;
    }

    public function getLastSyncError(): string
    {
        return $this->lastSyncError;
    }

    public function setLastSyncError(string $lastSyncError): void
    {
        $this->lastSyncError = $lastSyncError;
    }
}
