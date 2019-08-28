<?php
declare(strict_types = 1);

namespace Brotkrueml\JobRouterData\Command;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Synchroniser\TableSynchroniser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncCommand extends Command
{
    /** @var TableSynchroniser */
    private $tableSynchroniser;

    protected function configure(): void
    {
        $this
            ->setDescription('Synchronises JobData tables')
            ->setHelp('This command synchronises JobData tables from JobRouter instances into TYPO3. You can set a specific table name as argument. If the table argument is omitted, all enabled tables are processed.')
            ->addArgument('table', InputArgument::OPTIONAL, 'The name of the table (optional)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);

        try {
            $tableSynchroniser = $this->getTableSynchroniser();
            $tableSynchroniser->synchronise($input->getArgument('table'));
        } catch (\Exception $e) {
            $outputStyle->error($e->getMessage());
            return 1;
        }

        $outputStyle->success('Table(s) are synchronised successfully');

        return 0;
    }

    protected function getTableSynchroniser(): TableSynchroniser
    {
        if ($this->tableSynchroniser) {
            return $this->tableSynchroniser;
        }

        return new TableSynchroniser();
    }

    /**
     * For testing purposes only!
     *
     * @param TableSynchroniser $tableSynchroniser
     *
     * @internal
     */
    public function setTableSynchroniser(TableSynchroniser $tableSynchroniser)
    {
        $this->tableSynchroniser = $tableSynchroniser;
    }
}
