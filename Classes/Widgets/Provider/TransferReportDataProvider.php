<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Widgets\Provider;

use Brotkrueml\JobRouterBase\Domain\Model\TransferReportItem;
use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

/**
 * @internal
 */
final class TransferReportDataProvider implements ListDataProviderInterface
{
    /**
     * @var TransferRepository
     */
    private $transferRepository;

    public function __construct(TransferRepository $transferRepository)
    {
        $this->transferRepository = $transferRepository;
    }

    public function getItems(): array
    {
        $transfers = $this->transferRepository->findErroneousTransfers();

        $items = [];
        foreach ($transfers as $transfer) {
            /** @var Transfer $transfer */
            $items[] = new TransferReportItem(
                $transfer->getCrdate(),
                $transfer->getTransmitMessage(),
                $transfer->getCorrelationId()
            );
        }

        return $items;
    }
}
