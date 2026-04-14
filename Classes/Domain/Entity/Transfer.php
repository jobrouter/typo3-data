<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Domain\Entity;

final readonly class Transfer
{
    public function __construct(
        public int $uid,
        public int $crdate,
        public int $tableUid,
        public string $correlationId,
        public string $data,
        public bool $transmitSuccess,
        public ?\DateTimeImmutable $transmitDate,
        public string $transmitMessage,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $transmitDate = (int) $data['transmit_date'];

        return new self(
            (int) $data['uid'],
            (int) $data['crdate'],
            (int) $data['table_uid'],
            $data['correlation_id'],
            (string) $data['data'],
            (bool) ($data['transmit_success'] ?? false),
            $transmitDate > 0 ? (new \DateTimeImmutable())->setTimestamp($transmitDate) : null,
            (string) $data['transmit_message'],
        );
    }
}
