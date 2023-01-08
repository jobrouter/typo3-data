<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Entity;

final class Column
{
    public function __construct(
        public readonly int $uid,
        public readonly string $name,
        public readonly string $label,
        public readonly int $type,
        public readonly int $decimalPlaces,
        public readonly int $fieldSize,
        public readonly string $alignment,
        public readonly int $sortingPriority,
        public readonly string $sortingOrder
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['uid'],
            $data['name'],
            $data['label'],
            (int)$data['type'],
            (int)$data['decimal_places'],
            (int)$data['field_size'],
            $data['alignment'],
            (int)$data['sorting_priority'],
            $data['sorting_order']
        );
    }
}
