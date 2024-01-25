<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Domain\Entity;

final class Transfer
{
    public function __construct(
        public readonly int $uid,
        public readonly int $crdate,
        public readonly int $tableUid,
        public readonly string $correlationId,
        public readonly string $data,
        public readonly bool $transmitSuccess,
        public readonly ?\DateTimeImmutable $transmitDate,
        public readonly string $transmitMessage,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $transmitDate = (int)$data['transmit_date'];

        return new self(
            (int)$data['uid'],
            (int)$data['crdate'],
            (int)$data['table_uid'],
            $data['correlation_id'],
            (string)$data['data'],
            (bool)($data['transmit_success'] ?? false),
            $transmitDate > 0 ? (new \DateTimeImmutable())->setTimestamp($transmitDate) : null,
            (string)$data['transmit_message'],
        );
    }
}
