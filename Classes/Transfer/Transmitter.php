<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Transfer;

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Entity\CountResult;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use Brotkrueml\JobRouterData\Domain\Repository\JobRouter\JobDataRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Exception\TableNotAvailableException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @internal Only to be used within the jobrouter_data extension, not part of the public API
 */
class Transmitter implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    private int $totalTransfers = 0;
    private int $erroneousTransfers = 0;

    public function __construct(
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly RestClientFactory $restClientFactory,
        private readonly TransferRepository $transferRepository,
        private readonly TableRepository $tableRepository
    ) {
    }

    public function run(): CountResult
    {
        $this->logger->info('Transmit data sets for all tables');
        $transfers = $this->transferRepository->findByTransmitSuccess(0);

        $this->totalTransfers = 0;
        $this->erroneousTransfers = 0;
        foreach ($transfers as $transfer) {
            $this->processTransfer($transfer);
        }

        $this->logger->info(
            \sprintf(
                'Transmitted %d transfer(s) with %d errors',
                $this->totalTransfers,
                $this->erroneousTransfers
            )
        );

        return new CountResult($this->totalTransfers, $this->erroneousTransfers);
    }

    private function processTransfer(Transfer $transfer): void
    {
        $this->logger->debug(\sprintf('Processing transfer with uid "%d"', $transfer->getUid()));

        $this->totalTransfers++;
        try {
            $this->transmitTransfer($transfer);
        } catch (\Exception $e) {
            $this->erroneousTransfers++;
            $context = [
                'transfer uid' => $transfer->getUid(),
                'exception class' => $e::class,
                'exception code' => $e->getCode(),
            ];
            $this->logger->error($e->getMessage(), $context);
            $transfer->setTransmitMessage($e->getMessage());
        }

        $transfer->setTransmitDate(new \DateTime());
        $this->transferRepository->update($transfer);
        $this->persistenceManager->persistAll();
    }

    private function transmitTransfer(Transfer $transfer): void
    {
        $result = $this
            ->getJobDataRepositoryForTableUid($transfer->getTableUid())
            ->add(\json_decode($transfer->getData(), true, 512, \JSON_THROW_ON_ERROR));
        $jrid = $result[0]['jrid'] ?? null;

        $transfer->setTransmitSuccess(true);
        $transfer->setTransmitMessage($jrid ? \json_encode([
            'jrid' => $jrid,
        ], \JSON_THROW_ON_ERROR) : '');
    }

    private function getJobDataRepositoryForTableUid(int $tableUid): JobDataRepository
    {
        /** @var JobDataRepository[] $jobDataRepositories */
        static $jobDataRepositories = [];

        $table = $this->getTable($tableUid);

        if ($jobDataRepositories[$tableUid] ?? false) {
            return $jobDataRepositories[$tableUid];
        }

        return $jobDataRepositories[$tableUid] = new JobDataRepository(
            $this->restClientFactory,
            $this->tableRepository,
            $table->getHandle()
        );
    }

    private function getTable(int $tableUid): Table
    {
        /** @var Table|null $table */
        $table = $this->tableRepository->findByIdentifier($tableUid);

        if (! $table instanceof Table) {
            throw new TableNotAvailableException(
                \sprintf(
                    'Table link with uid "%d" is not available',
                    $tableUid
                ),
                1579886642
            );
        }

        return $table;
    }
}
