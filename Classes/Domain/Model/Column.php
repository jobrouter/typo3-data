<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Column model
 */
class Column extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var int
     */
    protected $type = 0;

    /**
     * @var int
     */
    protected $decimalPlaces = 0;

    /**
     * @var int
     */
    protected $fieldSize = 0;

    /**
     * @var string
     */
    protected $alignment = '';

    /**
     * @var int
     */
    protected $sortingPriority = 0;

    /**
     * @var string
     */
    protected $sortingOrder = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getDecimalPlaces(): int
    {
        return $this->decimalPlaces;
    }

    public function setDecimalPlaces(int $decimalPlaces): void
    {
        $this->decimalPlaces = $decimalPlaces;
    }

    public function getFieldSize(): int
    {
        return $this->fieldSize;
    }

    public function setFieldSize(int $fieldSize): void
    {
        $this->fieldSize = $fieldSize;
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    public function setAlignment(string $alignment): void
    {
        $this->alignment = $alignment;
    }

    public function getSortingPriority(): int
    {
        return $this->sortingPriority;
    }

    public function setSortingPriority(int $sortingPriority): void
    {
        $this->sortingPriority = $sortingPriority;
    }

    public function getSortingOrder(): string
    {
        return $this->sortingOrder;
    }

    public function setSortingOrder(string $sortingOrder): void
    {
        $this->sortingOrder = $sortingOrder;
    }
}
