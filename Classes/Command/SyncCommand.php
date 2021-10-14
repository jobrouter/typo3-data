<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Command;

use Brotkrueml\JobRouterData\Extension;
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

/**
 * @internal
 */
final class SyncCommand extends Command
{
    public const EXIT_CODE_OK = 0;
    public const EXIT_CODE_ERRORS_ON_SYNCHRONISATION = 1;
    public const EXIT_CODE_CANNOT_ACQUIRE_LOCK = 2;

    private const ARGUMENT_TABLE = 'table';

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var SynchronisationRunner
     */
    private $synchronisationRunner;

    public function __construct(
        LockFactory $lockFactory,
        Registry $registry,
        SynchronisationRunner $synchronisationRunner
    ) {
        $this->lockFactory = $lockFactory;
        $this->registry = $registry;
        $this->synchronisationRunner = $synchronisationRunner;

        parent::__construct();
    }

    protected function configure(): void
    {
        // @todo Remove when compatibility is set to TYPO3 v11+ as it is defined in Configuration/Services.yaml
        $this
            ->setDescription('Synchronise JobData data sets from JobRouter installations')
            ->setHelp('This command synchronises JobData tables from JobRouter instances into TYPO3. You can set a specific table name as argument. If the table argument is omitted, all enabled tables are processed.')
            ->addArgument(self::ARGUMENT_TABLE, InputArgument::OPTIONAL, 'The handle of a table (optional)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTime = time();
        $outputStyle = new SymfonyStyle($input, $output);

        try {
            $locker = $this->lockFactory->createLocker(self::class);
            $locker->acquire(LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK);

            $tableHandle = $input->getArgument(self::ARGUMENT_TABLE) ?: '';
            [$exitCode, $messageType, $message] = $this->runSynchronisation($tableHandle);
            $locker->release();
            $outputStyle->{$messageType}($message);
            $this->recordLastRun($exitCode);

            return $exitCode;
        } catch (LockException $e) {
            $outputStyle->warning('Could not acquire lock, another process is running');

            return self::EXIT_CODE_CANNOT_ACQUIRE_LOCK;
        }
    }

    /**
     * @return array<0: int, 1: string, 2: string>
     */
    private function runSynchronisation(string $tableHandle): array
    {
        [$total, $errors] = $this->synchronisationRunner->run($tableHandle);

        if ($errors) {
            $message = \sprintf('%d out of %d table(s) had errors on synchronisation', $errors, $total);

            return [
                self::EXIT_CODE_ERRORS_ON_SYNCHRONISATION,
                'warning',
                $message,
            ];
        }

        if ($tableHandle !== '') {
            $message = \sprintf('Table with handle "%s" synchronised successfully', $tableHandle);
        } else {
            $message = \sprintf('%d table(s) synchronised successfully', $total);
        }

        return [
            self::EXIT_CODE_OK,
            'success',
            $message,
        ];
    }

    private function recordLastRun(int $exitCode): void
    {
        $runInformation = [
            'start' => $this->startTime,
            'end' => time(),
            'exitCode' => $exitCode,
        ];
        $this->registry->set(Extension::REGISTRY_NAMESPACE, 'syncCommand.lastRun', $runInformation);
    }
}
