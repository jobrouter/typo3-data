<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Transfer;

use Brotkrueml\JobRouterClient\Client\ClientInterface;
use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Model\Transfer;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterData\Exception\ConnectionNotAvailableException;
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

    private const DATASET_RESOURCE_TEMPLATE = '/application/jobdata/tables/%s/datasets';

    /** @var PersistenceManagerInterface */
    private $persistenceManager;

    /** @var TransferRepository */
    private $transferRepository;

    /** @var TableRepository */
    private $tableRepository;

    /** @var RestClientFactory */
    private $restClientFactory;

    private static $clients = [];

    private $totalNumbersOfTransfers = 0;
    private $erroneousNumbersOfTransfers = 0;

    public function __construct(
        PersistenceManagerInterface $persistenceManager,
        RestClientFactory $restClientFactory,
        TransferRepository $transferRepository,
        TableRepository $tableRepository
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->restClientFactory = $restClientFactory;
        $this->transferRepository = $transferRepository;
        $this->tableRepository = $tableRepository;
    }

    public function run(): array
    {
        $this->logger->info('Transmit data sets for all tables');
        $transfers = $this->transferRepository->findByTransmitSuccess(0);

        $this->totalNumbersOfTransfers = 0;
        $this->erroneousNumbersOfTransfers = 0;
        foreach ($transfers as $transfer) {
            $this->processTransfer($transfer);
        }

        $this->logger->info(
            \sprintf(
                'Transmitted %d transfer(s) with %d errors',
                $this->totalNumbersOfTransfers,
                $this->erroneousNumbersOfTransfers
            )
        );

        return [$this->totalNumbersOfTransfers, $this->erroneousNumbersOfTransfers];
    }

    private function processTransfer(Transfer $transfer): void
    {
        $this->logger->debug(\sprintf('Processing transfer with uid "%d"', $transfer->getUid()));

        $this->totalNumbersOfTransfers++;
        try {
            $this->transmitTransfer($transfer);
        } catch (\Exception $e) {
            $this->erroneousNumbersOfTransfers++;
            $context = [
                'transfer uid' => $transfer->getUid(),
                'exception class' => \get_class($e),
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
        $table = $this->getTable($transfer->getTableUid());
        $client = $this->getRestClientForTable($table);
        $response = $client->request(
            'POST',
            \sprintf(self::DATASET_RESOURCE_TEMPLATE, $table->getTableGuid()),
            ['dataset' => \json_decode($transfer->getData(), true)]
        );

        $jrid = null;
        if ($body = \json_decode($response->getBody()->getContents(), true)) {
            $jrid = $body['datasets'][0]['jrid'] ?? null;
        }

        $transfer->setTransmitSuccess(true);
        $transfer->setTransmitMessage($jrid ? \json_encode(['jrid' => $jrid]) : '');
    }

    private function getTable(int $tableUid): Table
    {
        /** @var Table $table */
        $table = $this->tableRepository->findByIdentifier($tableUid);

        if (empty($table)) {
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

    private function getRestClientForTable(Table $table): ClientInterface
    {
        /** @var Connection $connection */
        $connection = $table->getConnection();
        if (empty($connection)) {
            throw new ConnectionNotAvailableException(
                \sprintf(
                    'Connection for table link "%s" with uid "%d" is not available',
                    $table->getName(),
                    $table->getUid()
                ),
                1579886904
            );
        }

        $connectionUid = $connection->getUid();
        if (static::$clients[$connectionUid] ?? false) {
            return static::$clients[$connectionUid];
        }

        return static::$clients[$connectionUid] = $this->restClientFactory->create($connection);
    }
}
