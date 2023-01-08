<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_data" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterData\Controller;

use Brotkrueml\JobRouterConnector\Domain\Repository\ConnectionRepository;
use Brotkrueml\JobRouterConnector\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactoryInterface;
use Brotkrueml\JobRouterData\Domain\Dto\TableTestResult;
use Brotkrueml\JobRouterData\Domain\Repository\TableRepository;
use Brotkrueml\JobRouterData\Exception\TableNotFoundException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @internal
 */
final class TableTestController
{
    public function __construct(
        private readonly ConnectionRepository $connectionRepository,
        private readonly TableRepository $tableRepository,
        private readonly RestClientFactoryInterface $restClientFactory,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (! \is_array($body)) {
            return $this->buildResponse('Request has no valid body!');
        }

        $tableId = (int)$body['tableId'];
        try {
            try {
                $table = $this->tableRepository->findByUidWithHidden($tableId);
            } catch (TableNotFoundException) {
                return $this->buildResponse(\sprintf('Table with ID "%s" not found!', $tableId));
            }

            try {
                $connection = $this->connectionRepository->findByUidWithHidden($table->connectionUid);
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
