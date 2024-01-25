<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Widgets\Provider;

use JobRouter\AddOn\Typo3Base\Domain\Dto\TransferReportItem;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Transfer;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TransferRepository;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

/**
 * @internal
 */
final class TransferReportDataProvider implements ListDataProviderInterface
{
    public function __construct(
        private readonly TransferRepository $transferRepository,
    ) {}

    /**
     * @return TransferReportItem[]
     */
    public function getItems(): array
    {
        $transfers = $this->transferRepository->findErroneousTransfers();

        $items = [];
        foreach ($transfers as $transfer) {
            /** @var Transfer $transfer */
            $items[] = new TransferReportItem(
                $transfer->crdate,
                $transfer->transmitMessage,
                $transfer->correlationId,
            );
        }

        return $items;
    }
}
