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
use Symfony\Component\Console\Input\InputOption;
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
    private const OPTION_FORCE = 'force';

    private ?int $startTime = null;
    private ?SymfonyStyle $outputStyle = null;

    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly Registry $registry,
        private readonly SynchronisationRunner $synchronisationRunner
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command synchronises JobData tables from JobRouter instances into TYPO3. You can set a specific table name as argument. If the table argument is omitted, all enabled tables are processed.')
            ->addArgument(self::ARGUMENT_TABLE, InputArgument::OPTIONAL, 'The handle of a table (optional)')
            ->addOption(self::OPTION_FORCE, null, InputOption::VALUE_NONE, 'Force a full synchronisation of all datasets');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTime = time();
        $this->outputStyle = new SymfonyStyle($input, $output);

        try {
            $locker = $this->lockFactory->createLocker(self::class);
            $locker->acquire(LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK);

            $tableHandle = $input->getArgument(self::ARGUMENT_TABLE) ?: '';
            $force = $input->getOption(self::OPTION_FORCE);
            $exitCode = $this->runSynchronisation($tableHandle, $force);
            $locker->release();
            $this->recordLastRun($exitCode);

            return $exitCode;
        } catch (LockException) {
            $this->outputStyle->note('Could not acquire lock, another process is running');

            return self::EXIT_CODE_CANNOT_ACQUIRE_LOCK;
        }
    }

    private function runSynchronisation(string $tableHandle, bool $force): int
    {
        $result = $this->synchronisationRunner->run($tableHandle, $force);

        if ($result->errors > 0) {
            $this->outputStyle->warning(
                \sprintf('%d out of %d table(s) had errors during processing', $result->errors, $result->total)
            );

            return self::EXIT_CODE_ERRORS_ON_SYNCHRONISATION;
        }

        $message = $tableHandle !== ''
            ? \sprintf('Table with handle "%s" processed', $tableHandle)
            : \sprintf('%d table(s) processed', $result->total);

        $this->outputStyle->success($message);

        return self::EXIT_CODE_OK;
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
