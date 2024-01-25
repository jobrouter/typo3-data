<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Event;

use JobRouter\AddOn\Typo3Data\Domain\Entity\Column;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use Psr\EventDispatcher\StoppableEventInterface;

final class ModifyColumnContentEvent implements StoppableEventInterface
{
    private bool $contentFormatted = false;

    /**
     * @param float|int|string|null $content
     */
    public function __construct(
        private readonly Table $table,
        private readonly Column $column,
        private $content,
        private readonly string $locale,
    ) {}

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    public function getContent(): float|int|string|null
    {
        return $this->content;
    }

    public function setContent(float|int|string|null $content): void
    {
        $this->content = $content;
        $this->contentFormatted = true;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isPropagationStopped(): bool
    {
        return $this->contentFormatted;
    }
}
