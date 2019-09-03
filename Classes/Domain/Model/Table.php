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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Table model
 */
class Table extends AbstractEntity
{
    /** @var string */
    protected $name = '';

    /** @var \Brotkrueml\JobRouterConnector\Domain\Model\Connection */
    protected $connection = '';

    /** @var string */
    protected $tableGuid = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterData\Domain\Model\Column>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $columns = null;

    /** @var bool */
    protected $disabled = false;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->columns = new ObjectStorage();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getConnection(): Connection
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

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
