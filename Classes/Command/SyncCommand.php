<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Command;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Synchronisation\SynchronisationRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncCommand extends Command
{
    private const ARGUMENT_TABLE = 'table';

    /** @var SynchronisationRunner */
    private $synchronisationRunner;

    protected function configure(): void
    {
        $this
            ->setDescription('Synchronises JobData tables')
            ->setHelp('This command synchronises JobData tables from JobRouter instances into TYPO3. You can set a specific table name as argument. If the table argument is omitted, all enabled tables are processed.')
            ->addArgument(static::ARGUMENT_TABLE, InputArgument::OPTIONAL, 'The uid of a table (optional)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);

        $tableUid = $input->getArgument(static::ARGUMENT_TABLE) ? (int)$input->getArgument(static::ARGUMENT_TABLE) : null;

        $synchronisationRunner = $this->getSynchronisationRunner();
        [$total, $errors] = $synchronisationRunner->run($tableUid);

        if ($errors) {
            $outputStyle->error(
                \sprintf('%d out of %d table(s) had errors on synchronisation', $errors, $total)
            );
            return 1;
        }

        if ($tableUid) {
            $outputStyle->success(\sprintf('Table with uid "%d" synchronised successfully', $tableUid));
        } else {
            $outputStyle->success(\sprintf('%d table(s) synchronised successfully', $total));
        }

        return 0;
    }

    protected function getSynchronisationRunner(): SynchronisationRunner
    {
        if ($this->synchronisationRunner) {
            return $this->synchronisationRunner;
        }

        return new SynchronisationRunner();
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
