<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Widgets\Provider;

use Brotkrueml\JobRouterBase\Domain\Model\TransferStatus;
use Brotkrueml\JobRouterBase\Widgets\Provider\TransferStatusDataProviderInterface;
use Brotkrueml\JobRouterData\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterData\Extension;
use TYPO3\CMS\Core\Registry;

/**
 * @internal
 */
final class TransferStatusDataProvider implements TransferStatusDataProviderInterface
{
    private readonly TransferStatus $status;

    public function __construct(
        private readonly Registry $registry,
        private readonly TransferRepository $transferRepository
    ) {
        $this->status = new TransferStatus();
    }

    public function getStatus(): TransferStatus
    {
        $this->calculateStatuses();
        $this->evaluateLastRun();
        $this->calculateNumberOfDays();

        return $this->status;
    }

    private function calculateStatuses(): void
    {
        $startSuccessCounts = $this->transferRepository->countGroupByTransmitSuccess();
        $toBeClassified = 0;
        foreach ($startSuccessCounts as $fields) {
            if ($fields['transmit_success'] === 0) {
                $toBeClassified = $fields['count'];
            } else {
                $this->status->setSuccessfulCount($fields['count']);
            }
        }

        if ($toBeClassified > 0) {
            $this->status->setFailedCount($this->transferRepository->countTransmitFailed());
            $this->status->setPendingCount($toBeClassified - $this->status->getFailedCount());
        }
    }

    private function evaluateLastRun(): void
    {
        $lastRunInformation = $this->registry->get(Extension::REGISTRY_NAMESPACE, 'transmitCommand.lastRun');
        if (! \is_array($lastRunInformation)) {
            return;
        }

        if ($lastRunInformation['start'] ?? false) {
            $this->status->setLastRun(
                (new \DateTime('@' . $lastRunInformation['start']))->setTimezone(new \DateTimeZone(date_default_timezone_get()))
            );
        }
    }

    private function calculateNumberOfDays(): void
    {
        $firstCreationDate = $this->transferRepository->findFirstCreationDate();
        if ($firstCreationDate === 0) {
            return;
        }

        $firstCreationDateTime = new \DateTimeImmutable('@' . $firstCreationDate);
        $recentDateTime = new \DateTimeImmutable();
        $difference = $recentDateTime->diff($firstCreationDateTime);
        $this->status->setNumberOfDays($difference->days + 1);
    }
}
