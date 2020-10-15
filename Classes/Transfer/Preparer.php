<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Transfer;

use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Exception\PrepareException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * @api
 */
final class Preparer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var PersistenceManager
     */
    private $persistenceManager;

    /**
     * @var TransferRepository
     */
    private $transferRepository;

    public function __construct(PersistenceManager $persistenceManager, TransferRepository $transferRepository)
    {
        $this->persistenceManager = $persistenceManager;
        $this->transferRepository = $transferRepository;
    }

    public function store(int $tableUid, string $identifier, string $data): void
    {
        $transfer = new Transfer();
        $transfer->setPid(0);
        $transfer->setTableUid($tableUid);
        $transfer->setIdentifier($identifier);
        $transfer->setData($data);

        try {
            $this->transferRepository->add($transfer);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new PrepareException('Transfer record cannot be written', 1579789397, $e);
        }
    }
}
