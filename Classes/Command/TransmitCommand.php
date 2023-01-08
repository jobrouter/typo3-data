<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Command;

use Brotkrueml\JobRouterData\Transfer\Transmitter;
use Symfony\Component\Console\Command\Command;
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
final class TransmitCommand extends Command
{
    private ?int $startTime = null;
    private ?SymfonyStyle $outputStyle = null;

    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly Registry $registry,
        private readonly Transmitter $transmitter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command transmits data sets from TYPO3 to JobData tables in JobRouter installations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTime = \time();
        $this->outputStyle = new SymfonyStyle($input, $output);

        try {
            $locker = $this->lockFactory->createLocker(self::class);
            $locker->acquire(LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK);

            $exitCode = $this->runTransmitter();
            $locker->release();
            $this->recordLastRun($exitCode);

            return $exitCode;
        } catch (LockException) {
            $this->outputStyle->note('Could not acquire lock, another process is running');

            return self::FAILURE;
        }
    }

    private function runTransmitter(): int
    {
        $result = $this->transmitter->run();

        if ($result->errors > 0) {
            $this->outputStyle->warning(
                \sprintf('%d out of %d transfer(s) had errors on transmission', $result->errors, $result->total),
            );

            return self::FAILURE;
        }

        $this->outputStyle->success(
            \sprintf('%d transfer(s) transmitted successfully', $result->total),
        );

        return self::SUCCESS;
    }

    private function recordLastRun(int $exitCode): void
    {
        $runInformation = [
            'start' => $this->startTime,
            'end' => \time(),
            'exitCode' => $exitCode,
        ];
        $this->registry->set('tx_jobrouter_data', 'transmitCommand.lastRun', $runInformation);
    }
}
