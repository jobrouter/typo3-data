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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Table model
 */
class Table extends AbstractEntity
{
    public const TYPE_SIMPLE = 1;
    public const TYPE_CUSTOM_TABLE = 2;
    public const TYPE_OTHER_USAGE = 3;
    public const TYPE_FORM_FINISHER = 4;

    /**
     * @var int
     */
    protected $type = 0;

    /**
     * @var string
     */
    protected $handle = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var \Brotkrueml\JobRouterConnector\Domain\Model\Connection|null
     */
    protected $connection;

    /**
     * @var string
     */
    protected $tableGuid = '';

    /**
     * @var string
     */
    protected $customTable = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Column>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $columns;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Dataset>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $datasets;

    /**
     * @var string
     */
    protected $datasetsSyncHash = '';

    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var \DateTime|null
     */
    protected $lastSyncDate;

    /**
     * @var string
     */
    protected $lastSyncError = '';

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
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Column>
     */
    public function getColumns(): ObjectStorage
    {
        return $this->columns;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Column> $columns
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
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Dataset>
     */
    public function getDatasets(): ObjectStorage
    {
        return $this->datasets;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Dataset> $datasets
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
