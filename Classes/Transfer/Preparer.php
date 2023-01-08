<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Transfer;

use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Exception\PrepareException;
use Psr\Log\LoggerInterface;

/**
 * @api
 */
final class Preparer
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TransferRepository $transferRepository
    ) {
    }

    public function store(int $tableUid, string $correlationId, string $data): void
    {
        try {
            $this->transferRepository->add($tableUid, $correlationId, $data, new \DateTimeImmutable());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new PrepareException('Transfer record cannot be stored', 1579789397, $e);
        }
    }
}
