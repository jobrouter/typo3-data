<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterData\Controller;

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterData\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TableAjaxController
{
    /** @var ObjectManager */
    private $objectManager;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    public function checkAction(ServerRequestInterface $request): ResponseInterface
    {
        $tableId = (int)$request->getParsedBody()['tableId'];

        $result = ['check' => 'ok'];
        try {
            $tableRepository = $this->objectManager->get(TableRepository::class);
            /** @var Table $table */
            $table = $tableRepository->findByIdentifierWithHidden($tableId);

            if ($table) {
                try {
                    (new RestClientFactory())->create($table->getConnection())->request(
                        'GET',
                        \sprintf('application/jobdata/tables/%s/datasets', $table->getTableGuid())
                    );
                } catch (\Exception $e) {
                    $result = ['error' => $e->getMessage()];
                }
            } else {
                $result = ['error' => \sprintf('Table with id %s not found!', $tableId)];
            }
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage()];
        }

        return new JsonResponse($result);
    }
}
