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
    private bool $contentFormatted = false;

    /**
     * @param float|int|string|null $content
     */
    public function __construct(
        private readonly Table $table,
        private readonly Column $column,
        private $content,
        private readonly string $locale
    ) {
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
