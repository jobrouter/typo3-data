<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Command;

use Brotkrueml\JobRouterData\Exception\DeleteException;
use Brotkrueml\JobRouterData\Transfer\Deleter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class CleanUpTransfersCommand extends Command
{
    public const EXIT_CODE_OK = 0;
    public const EXIT_CODE_INVALID_ARGUMENT = 1;
    public const EXIT_CODE_DELETION_FAILED = 2;

    private const ARGUMENT_AGE_IN_DAYS = 'ageInDays';
    private const DEFAULT_AGE_IN_DAYS = 30;

    /** @var Deleter */
    private $deleter;

    public function __construct(Deleter $deleter)
    {
        $this->deleter = $deleter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $help = \sprintf(
            'This command deletes successful transfers. If the parameter "%s" is omitted transfers older than %d days are deleted.',
            self::ARGUMENT_AGE_IN_DAYS,
            self::DEFAULT_AGE_IN_DAYS
        );

        $this
            ->setDescription('Delete old entries in the transfer table')
            ->setHelp($help)
            ->addArgument(
                self::ARGUMENT_AGE_IN_DAYS,
                InputArgument::OPTIONAL,
                'The age in days (optional). Set to 0 to delete all successful transfers.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);

        try {
            $ageInDays = $this->getAgeInDays($input);
        } catch (\InvalidArgumentException $e) {
            $outputStyle->error($e->getMessage());

            return self::EXIT_CODE_INVALID_ARGUMENT;
        }

        try {
            $numberOfDeletedTransfers = $this->deleter->run($ageInDays);
        } catch (DeleteException $e) {
            $outputStyle->error($e->getMessage());

            return self::EXIT_CODE_DELETION_FAILED;
        }

        if ($numberOfDeletedTransfers === 0) {
            $message = \sprintf(
                'No successful transfers older than %d days present',
                $ageInDays
            );
        } else {
            $message = \sprintf(
                '%d successful transfer%s older than %d days deleted',
                $numberOfDeletedTransfers,
                $numberOfDeletedTransfers > 1 ? 's' : '',
                $ageInDays
            );
        }

        $outputStyle->success($message);

        return self::EXIT_CODE_OK;
    }

    protected function getAgeInDays(InputInterface $input): int
    {
        $ageInDays = $input->getArgument(self::ARGUMENT_AGE_IN_DAYS) ?? self::DEFAULT_AGE_IN_DAYS;
        if (!\is_numeric($ageInDays)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Argument "%s" must be a number, "%s" given',
                    self::ARGUMENT_AGE_IN_DAYS,
                    $ageInDays
                ),
                1582139568
            );
        }

        $ageInDays = (int)$ageInDays;
        if ($ageInDays < 0) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Argument "%s" must not be a negative number, "%d" given',
                    self::ARGUMENT_AGE_IN_DAYS,
                    $ageInDays
                ),
                1582139576
            );
        }

        return $ageInDays;
    }
}
