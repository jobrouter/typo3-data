<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Controller;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * TableController
 */
class TableController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var TableRepository */
    protected $tableRepository;

    public function injectTableRepository(TableRepository $tableRepository): void
    {
        $this->tableRepository = $tableRepository;
    }

    public function showAction(): void
    {
        $tableUid = (int)$this->settings['table'];
        $table = $this->tableRepository->findByIdentifier((int)$this->settings['table']);

        if (!$table) {
            $this->logger->warning(
                \sprintf(
                    'The table with uid "%s" could not be found, perhaps it is disabled or deleted.',
                    $tableUid
                )
            );

            return;
        }

        $this->view->assignMultiple([
            'table' => $table,
            'settings' => $this->settings,
        ]);
    }
}
