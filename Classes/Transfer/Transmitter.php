<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Transfer;

use JobRouter\AddOn\Typo3Data\Domain\Dto\CountResult;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Table;
use JobRouter\AddOn\Typo3Data\Domain\Entity\Transfer;
use JobRouter\AddOn\Typo3Data\Domain\Repository\JobRouter\JobDataRepository;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Data\Exception\TableNotAvailableException;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * @internal Only to be used within the jobrouter_data extension, not part of the public API
 */
class Transmitter
{
    private int $totalTransfers = 0;
    private int $erroneousTransfers = 0;

    public function __construct(
        private readonly JobDataRepository $jobDataRepository,
        private readonly LoggerInterface $logger,
        private readonly TransferRepository $transferRepository,
        private readonly TableRepository $tableRepository,
    ) {}

    public function run(): CountResult
    {
        $this->logger->info('Transmit data sets for all tables');
        $transfers = $this->transferRepository->findNotTransmitted();

        $this->totalTransfers = 0;
        $this->erroneousTransfers = 0;
        foreach ($transfers as $transfer) {
            $this->processTransfer($transfer);
        }

        $this->logger->info(
            \sprintf(
                'Transmitted %d transfer(s) with %d errors',
                $this->totalTransfers,
                $this->erroneousTransfers,
            ),
        );

        return new CountResult($this->totalTransfers, $this->erroneousTransfers);
    }

    private function processTransfer(Transfer $transfer): void
    {
        $this->logger->debug(\sprintf('Processing transfer with uid "%d"', $transfer->uid));

        $this->totalTransfers++;
        try {
            $jrid = $this->transmitTransfer($transfer);
        } catch (\Exception $e) {
            $this->erroneousTransfers++;
            $context = [
                'transfer uid' => $transfer->uid,
                'exception class' => $e::class,
                'exception code' => $e->getCode(),
            ];
            $this->logger->error($e->getMessage(), $context);
            $this->transferRepository->updateTransmitData($transfer->uid, false, new \DateTimeImmutable(), $e->getMessage());
            return;
        }

        $message = \json_encode([
            'jrid' => $jrid,
        ], \JSON_THROW_ON_ERROR);
        $this->transferRepository->updateTransmitData($transfer->uid, true, new \DateTimeImmutable(), $message);
    }

    private function transmitTransfer(Transfer $transfer): int
    {
        $result = $this->jobDataRepository
            ->add(
                $this->getTable($transfer->tableUid)->handle,
                \json_decode($transfer->data, true, flags: \JSON_THROW_ON_ERROR),
            );

        return (int)($result[0]['jrid'] ?? 0);
    }

    private function getTable(int $tableUid): Table
    {
        try {
            return $this->tableRepository->findByUid($tableUid);
        } catch (TableNotFoundException) {
            throw new TableNotAvailableException(
                \sprintf(
                    'Table link with uid "%d" is not available',
                    $tableUid,
                ),
                1579886642,
            );
        }
    }
}
