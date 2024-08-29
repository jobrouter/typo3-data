<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Data\Controller;

use JobRouter\AddOn\Typo3Connector\Domain\Repository\ConnectionRepository;
use JobRouter\AddOn\Typo3Connector\Exception\ConnectionNotFoundException;
use JobRouter\AddOn\Typo3Connector\RestClient\RestClientFactoryInterface;
use JobRouter\AddOn\Typo3Data\Domain\Dto\TableTestResult;
use JobRouter\AddOn\Typo3Data\Domain\Repository\TableRepository;
use JobRouter\AddOn\Typo3Data\Exception\TableNotFoundException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Backend\Attribute\AsController;

/**
 * @internal
 */
#[AsController]
final class TableTestController
{
    public function __construct(
        private readonly ConnectionRepository $connectionRepository,
        private readonly TableRepository $tableRepository,
        private readonly RestClientFactoryInterface $restClientFactory,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (! \is_array($body)) {
            return $this->buildResponse('Request has no valid body!');
        }

        $tableId = (int) $body['tableId'];
        try {
            try {
                $table = $this->tableRepository->findByUidWithHidden($tableId);
            } catch (TableNotFoundException) {
                return $this->buildResponse(\sprintf('Table with ID "%s" not found!', $tableId));
            }

            try {
                $connection = $this->connectionRepository->findByUid($table->connectionUid, true);
            } catch (ConnectionNotFoundException) {
                return $this->buildResponse(\sprintf('Connection with ID "%s" not found!', $table->connectionUid));
            }

            $this->restClientFactory->create($connection)->request(
                'HEAD',
                \sprintf('application/jobdata/tables/%s/datasets', $table->tableGuid),
            );
            return $this->buildResponse();
        } catch (\Throwable $t) {
            return $this->buildResponse($t->getMessage());
        }
    }

    private function buildResponse(string $errorMessage = ''): ResponseInterface
    {
        $result = new TableTestResult($errorMessage);

        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($this->streamFactory->createStream($result->toJson()));
    }
}
