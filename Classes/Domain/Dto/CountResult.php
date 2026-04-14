<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Dto;

/**
 * @internal
 */
final readonly class CountResult
{
    public function __construct(
        public int $total,
        public int $errors,
    ) {}
}
