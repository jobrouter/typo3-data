<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Entity;

final class Dataset
{
    /**
     * @var array<string, mixed>
     */
    public readonly array $dataset;

    private function __construct(
        public readonly int $uid,
        public readonly int $tableUid,
        public readonly int $jrid,
        string $dataset,
    ) {
        $this->dataset = \json_decode($dataset, true, flags: \JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['uid'],
            (int)$data['table_uid'],
            (int)$data['jrid'],
            (string)$data['dataset'],
        );
    }
}
