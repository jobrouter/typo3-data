<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Entity;

final readonly class Content
{
    public function __construct(
        public int $uid,
        public string $flexForm,
    ) {}

    /**
     * @param array<string, mixed> $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            (int) $array['uid'],
            $array['pi_flexform'],
        );
    }
}
