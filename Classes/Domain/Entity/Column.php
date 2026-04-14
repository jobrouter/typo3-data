<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Entity;

final readonly class Column
{
    public function __construct(
        public int $uid,
        public string $name,
        public string $label,
        public int $type,
        public int $decimalPlaces,
        public int $fieldSize,
        public string $alignment,
        public int $sortingPriority,
        public string $sortingOrder,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['uid'],
            $data['name'],
            $data['label'],
            (int) $data['type'],
            (int) $data['decimal_places'],
            (int) $data['field_size'],
            $data['alignment'],
            (int) $data['sorting_priority'],
            $data['sorting_order'],
        );
    }
}
