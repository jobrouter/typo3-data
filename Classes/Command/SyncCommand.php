<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Command;

use Brotkrueml\JobRouterData\Synchronisation\SynchronisationRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Locking\Exception as LockException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class SyncCommand extends Command
{
    public const EXIT_CODE_OK = 0;
    public const EXIT_CODE_ERRORS_ON_SYNCHRONISATION = 1;
    public const EXIT_CODE_CANNOT_ACQUIRE_LOCK = 2;

    private const ARGUMENT_TABLE = 'table';

    /** @var int */
    private $startTime;

    /** @var LockingStrategyInterface */
    private $locker;

    /** @var SynchronisationRunner */
    private $synchronisationRunner;

    protected function configure(): void
    {
        $this
            ->setDescription('Synchronise JobData data sets from JobRouter installations')
            ->setHelp('This command synchronises JobData tables from JobRouter instances into TYPO3. You can set a specific table name as argument. If the table argument is omitted, all enabled tables are processed.')
            ->addArgument(static::ARGUMENT_TABLE, InputArgument::OPTIONAL, 'The uid of a table (optional)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTime = time();
        $outputStyle = new SymfonyStyle($input, $output);
        $lockFactory = GeneralUtility::makeInstance(LockFactory::class);

        try {
            $this->locker = $lockFactory->createLocker(__CLASS__, LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE);
            $this->locker->acquire(LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK);

            $tableUid = $input->getArgument(static::ARGUMENT_TABLE) ? (int)$input->getArgument(static::ARGUMENT_TABLE) : null;
            [$exitCode, $messageType, $message] = $this->runSynchronisation($tableUid);
            $this->locker->release();
            $outputStyle->{$messageType}($message);
            $this->recordLastRun($exitCode);

            return $exitCode;
        } catch (LockException $e) {
            $outputStyle->warning('Could not acquire lock, another process is running');

            return self::EXIT_CODE_CANNOT_ACQUIRE_LOCK;
        }
    }

    private function runSynchronisation(?int $tableUid): array
    {
        $synchronisationRunner = $this->getSynchronisationRunner();
        [$total, $errors] = $synchronisationRunner->run($tableUid);

        if ($errors) {
            $message = \sprintf('%d out of %d table(s) had errors on synchronisation', $errors, $total);

            return [
                self::EXIT_CODE_ERRORS_ON_SYNCHRONISATION,
                'warning',
                $message,
            ];
        }

        if ($tableUid) {
            $message = \sprintf('Table with uid "%d" synchronised successfully', $tableUid);
        } else {
            $message = \sprintf('%d table(s) synchronised successfully', $total);
        }

        return [
            self::EXIT_CODE_OK,
            'success',
            $message,
        ];
    }

    private function getSynchronisationRunner(): SynchronisationRunner
    {
        if ($this->synchronisationRunner) {
            return $this->synchronisationRunner;
        }

        return new SynchronisationRunner();
    }

    private function recordLastRun(int $exitCode): void
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        $runInformation = [
            'start' => $this->startTime,
            'end' => time(),
            'exitCode' => $exitCode,
        ];
        $registry->set('tx_jobrouter_data', 'syncCommand.lastRun', $runInformation);
    }

    /**
     * For testing purposes only!
     *
     * @param SynchronisationRunner $synchronisationRunner
     *
     * @internal
     */
    public function setSynchronisationRunner(SynchronisationRunner $synchronisationRunner)
    {
        $this->synchronisationRunner = $synchronisationRunner;
    }
}
