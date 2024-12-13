<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\SuccessJsonRpcResponse;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Exceptions\PayloadTooLargeException;
use Bouledepate\JsonRpc\Exceptions\TooManyRequestsException;
use Bouledepate\JsonRpc\Model\Dataset;
use Bouledepate\JsonRpc\Stack\ResponseItem;
use Bouledepate\JsonRpc\Stack\ResponseStack;

use GuzzleHttp\Psr7\Stream;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Throwable;

/**
 * @package Bouledepate\JsonRpc
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcBatchMiddleware extends JsonRpcBaseMiddleware
{
    private readonly ResponseStack $stack;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->stack = new ResponseStack();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->validatePayloadSize($request);
        $dataset = new Dataset($request);

        if (!$dataset->isBatchRequest()) {
            $this->processSingleRequest($request, $handler, $dataset->getData());
            return $this->createSingleResponse();
        }

        $this->validateBatchSize($dataset);
        $this->processBatchRequests($dataset, $request, $handler);

        return $this->createBatchResponse();
    }

    private function processBatchRequests(
        Dataset $dataset,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): void {
        foreach ($dataset->getData() as $requestData) {
            $this->processSingleRequest($request, $handler, $requestData);
        }
    }

    private function processSingleRequest(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        array $requestData
    ): void {
        $jrpcRequest = null;
        try {
            $localRequest = $this->prepareRequest($request, $requestData);
            $localDataset = new Dataset($localRequest);

            $this->validator->validate($localDataset);

            $jrpcRequest = $this->collectJsonRpcRequest($localDataset);

            if (!$this->isMethodAvailable($jrpcRequest)) {
                throw new MethodNotFoundException();
            }

            $response = $handler->handle($localRequest->withAttribute(JsonRpcRequest::class, $jrpcRequest));

            $this->stack->push($jrpcRequest, $this->createSuccessResponse($jrpcRequest, $response));
        } catch (Throwable $exception) {
            $jrpcRequest ??= new JsonRpcRequest(
                id: null,
                method: null,
                isNotification: isset($localDataset) && !$localDataset->hasProperty('id')
            );

            $this->stack->push($jrpcRequest, $this->createErrorResponse($jrpcRequest, $exception));
        }
    }

    private function prepareRequest(ServerRequestInterface $request, array $requestData): ServerRequestInterface
    {
        $localRequest = clone $request;

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Failed to open temporary stream.');
        }

        fwrite($stream, json_encode($requestData));
        rewind($stream);

        $newBody = new Stream($stream);

        return $localRequest->withBody($newBody);
    }

    private function createSuccessResponse(
        JsonRpcRequest $jrpcRequest,
        ResponseInterface $response
    ): SuccessJsonRpcResponse {
        $decodedResponse = $this->decodeResponseContent($response);
        return new SuccessJsonRpcResponse($jrpcRequest->getId(), $decodedResponse);
    }

    private function createErrorResponse(JsonRpcRequest $jrpcRequest, Throwable $exception): ErrorJsonRpcResponse
    {
        $errorData = $this->formatter->formatError($exception);
        return new ErrorJsonRpcResponse($jrpcRequest->getId(), $errorData);
    }

    private function createBatchResponse(): ResponseInterface
    {
        if ($this->stack->isEmpty()) {
            return $this->responseFactory->createResponse(204);
        }

        if ($this->stack->isSingleResponse()) {
            return $this->createSingleResponse();
        }

        return $this->createMultiResponse();
    }

    private function createSingleResponse(): ResponseInterface
    {
        $stackItem = $this->stack->pop();

        if ($stackItem->isNotificationResponse()) {
            return $this->responseFactory->createResponse(204);
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(
            json_encode($stackItem->getResponse(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function createMultiResponse(): ResponseInterface
    {
        $responseBody = array_filter(array_map(function (ResponseItem $item) {
            return $item->isNotificationResponse() ? null : $item->getResponse()->toArray();
        }, $this->stack->all()));

        if (empty($responseBody)) {
            return $this->responseFactory->createResponse(204);
        } elseif (count($responseBody) === 1) {
            $responseBody = array_pop($responseBody);
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($responseBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function collectJsonRpcRequest(Dataset $dataset): JsonRpcRequest
    {
        return new JsonRpcRequest(
            id: $dataset->getProperty('id'),
            method: $dataset->getProperty('method'),
            params: $dataset->getProperty('params'),
            isNotification: !$dataset->hasProperty('id')
        );
    }

    private function decodeResponseContent(ResponseInterface $response): mixed
    {
        $rawContent = (string)$response->getBody();
        return $rawContent === '' ? null : json_decode($rawContent, true);
    }

    private function validateBatchSize(Dataset $dataset): void
    {
        $batchSize = $this->options->getBatchSize();

        if (count($dataset->getData()) > $batchSize) {
            throw new TooManyRequestsException(content: [
                'actual_count' => count($dataset->getData()),
                'allowed_count' => $batchSize
            ]);
        }
    }
}
