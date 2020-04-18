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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class TransmitCommand extends Command
{
    public const EXIT_CODE_OK = 0;
    public const EXIT_CODE_ERRORS_ON_TRANSMISSION = 1;
    public const EXIT_CODE_CANNOT_ACQUIRE_LOCK = 2;

    /** @var int */
    private $startTime;

    /** @var LockingStrategyInterface */
    private $locker;

    protected function configure(): void
    {
        $this
            ->setDescription('Transmit data sets to JobData tables')
            ->setHelp('This command transmits data sets from TYPO3 to JobData tables in JobRouter installations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTime = time();
        $outputStyle = new SymfonyStyle($input, $output);
        $lockFactory = GeneralUtility::makeInstance(LockFactory::class);

        try {
            $this->locker = $lockFactory->createLocker(__CLASS__, LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE);
            $this->locker->acquire(LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK);

            [$exitCode, $messageType, $message] = $this->runTransmitter();
            $this->locker->release();
            $outputStyle->{$messageType}($message);
            $this->recordLastRun($exitCode);

            return $exitCode;
        } catch (LockException $e) {
            $outputStyle->warning('Could not acquire lock, another process is running');

            return self::EXIT_CODE_CANNOT_ACQUIRE_LOCK;
        }
    }

    /**
     * @return array with [exitCode, outputStyleType, outputMessage]
     */
    private function runTransmitter(): array
    {
        $transmitter = GeneralUtility::makeInstance(Transmitter::class);
        [$total, $errors] = $transmitter->run();

        if ($errors) {
            $message = \sprintf('%d out of %d transfer(s) had errors on transmission', $errors, $total);

            return [
                self::EXIT_CODE_ERRORS_ON_TRANSMISSION,
                'warning',
                $message,
            ];
        }

        $message = \sprintf('%d transfer(s) transmitted successfully', $total);

        return [
            self::EXIT_CODE_OK,
            'success',
            $message,
        ];
    }

    private function recordLastRun(int $exitCode): void
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        $runInformation = [
            'start' => $this->startTime,
            'end' => time(),
            'exitCode' => $exitCode,
        ];
        $registry->set('tx_jobrouter_data', 'transmitCommand.lastRun', $runInformation);
    }
}
