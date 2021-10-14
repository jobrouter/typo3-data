<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Controller;

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterData\Domain\Model\Table;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @internal
 */
final class TableAjaxController
{
    /**
     * @var TableRepository
     */
    private $tableRepository;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    public function __construct(
        TableRepository $tableRepository,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->tableRepository = $tableRepository;
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function checkAction(ServerRequestInterface $request): ResponseInterface
    {
        $tableId = (int)$request->getParsedBody()['tableId'];

        $result = [
            'check' => 'ok',
        ];
        try {
            /** @var Table $table */
            $table = $this->tableRepository->findByIdentifierWithHidden($tableId);

            if ($table) {
                try {
                    (new RestClientFactory())->create($table->getConnection())->request(
                        'GET',
                        \sprintf('application/jobdata/tables/%s/datasets', $table->getTableGuid())
                    );
                } catch (\Exception $e) {
                    $result = [
                        'error' => $e->getMessage(),
                    ];
                }
            } else {
                $result = [
                    'error' => \sprintf('Table with id %s not found!', $tableId),
                ];
            }
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
            ];
        }

        return $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($this->streamFactory->createStream(\json_encode($result)));
    }
}
