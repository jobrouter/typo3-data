<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Event;

use Brotkrueml\JobRouterData\Domain\Model\Column;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Psr\EventDispatcher\StoppableEventInterface;

final class ModifyColumnContentEvent implements StoppableEventInterface
{
    private Table $table;
    private Column $column;
    /**
     * @var float|int|string|null
     */
    private $content;
    private bool $contentFormatted = false;
    private string $locale;

    /**
     * @param float|int|string|null $content
     */
    public function __construct(Table $table, Column $column, $content, string $locale)
    {
        $this->table = $table;
        $this->column = $column;
        $this->content = $content;
        $this->locale = $locale;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    /**
     * @return float|int|string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param float|int|string|null $content
     */
    public function setContent($content): void
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
