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
use Bouledepate\JsonRpc\Stack\JsonRpcResponseItem;
use Bouledepate\JsonRpc\Stack\JsonRpcResponseStack;
use Nyholm\Psr7\Stream;
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
    /**
     * The stack for storing request-response pairs.
     *
     * @var JsonRpcResponseStack
     */
    private readonly JsonRpcResponseStack $stack;

    /**
     * Initializes the middleware and creates a response stack.
     *
     * @param ContainerInterface $container The dependency injection container.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->stack = new JsonRpcResponseStack();
    }

    /**
     * Main method for processing JSON-RPC batch requests.
     *
     * Checks if the request is a batch request, then processes each individual
     * request. Builds and returns a unified response for all requests.
     *
     * @param ServerRequestInterface  $request The original HTTP request.
     * @param RequestHandlerInterface $handler The handler to process the request.
     *
     * @return ResponseInterface HTTP response with the results of the batch requests.
     *
     * @throws MethodNotFoundException If requested method is not found or not available.
     * @throws ParseErrorException If there is an error parsing the request.
     * @throws PayloadTooLargeException If the payload size exceeds the configured limit.
     * @throws TooManyRequestsException If the number of requests in the batch exceeds the configured limit.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->validatePayloadSize($request);

        $dataset = new Dataset($request);

        if (!$dataset->isBatchRequest()) {
            $this->processSingleRequest($request, $dataset->getContent(), $handler);
            return $this->createSingleResponse();
        }

        $this->validateBatchSize($dataset);
        $this->processBatchRequests($dataset, $request, $handler);

        return $this->createBatchResponse();
    }

    /**
     * Processes each request in the batch.
     *
     * @param Dataset                 $dataset The dataset representing the batch request.
     * @param ServerRequestInterface  $request The original HTTP request.
     * @param RequestHandlerInterface $handler The handler to process each request.
     *
     * @throws MethodNotFoundException
     */
    private function processBatchRequests(Dataset $dataset, ServerRequestInterface $request, RequestHandlerInterface $handler): void
    {
        foreach ($dataset->getContent() as $requestData) {
            $this->processSingleRequest($request, $requestData, $handler);
        }
    }

    /**
     * Processes a single request in the batch.
     *
     * Validates the request, checks method availability, and adds the result
     * to the response stack.
     *
     * @param ServerRequestInterface  $request     The original HTTP request.
     * @param array                   $requestData Data for a single JSON-RPC request.
     * @param RequestHandlerInterface $handler     The handler to process the request.
     *
     * @throws MethodNotFoundException
     */
    private function processSingleRequest(
        ServerRequestInterface $request,
        array $requestData,
        RequestHandlerInterface $handler
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


    /**
     * Prepares a cloned request with new data.
     *
     * @param ServerRequestInterface $request     The original HTTP request.
     * @param array                  $requestData JSON-RPC data to write to the body of the cloned request.
     *
     * @return ServerRequestInterface The cloned request with a new body.
     */
    private function prepareRequest(ServerRequestInterface $request, array $requestData): ServerRequestInterface
    {
        $localRequest = clone $request;
        $newBody = Stream::create(fopen('php://temp', 'r+'));
        $newBody->write(json_encode($requestData));
        $newBody->rewind();

        return $localRequest->withBody($newBody);
    }

    /**
     * Creates a successful JSON-RPC response.
     *
     * @param JsonRpcRequest    $jrpcRequest The JSON-RPC request.
     * @param ResponseInterface $response    The HTTP response.
     *
     * @return SuccessJsonRpcResponse A JSON-RPC success response object.
     */
    private function createSuccessResponse(JsonRpcRequest $jrpcRequest, ResponseInterface $response): SuccessJsonRpcResponse
    {
        $decodedResponse = $this->decodeResponseContent($response);
        return new SuccessJsonRpcResponse($jrpcRequest->getId(), $decodedResponse);
    }

    /**
     * Creates a JSON-RPC error response.
     *
     * @param JsonRpcRequest $jrpcRequest The JSON-RPC request.
     * @param Throwable      $exception   The exception containing error details.
     *
     * @return ErrorJsonRpcResponse A JSON-RPC error response object.
     */
    private function createErrorResponse(JsonRpcRequest $jrpcRequest, Throwable $exception): ErrorJsonRpcResponse
    {
        $errorData = $this->formatter->formatError($exception);
        return new ErrorJsonRpcResponse($jrpcRequest->getId(), $errorData);
    }

    /**
     * Creates the HTTP response for a batch request.
     *
     * Depending on the contents of the response stack, this method builds a
     * response with one or multiple JSON-RPC responses.
     *
     * @return ResponseInterface The HTTP response with the batch results.
     */
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

    /**
     * Creates an HTTP response for a single JSON-RPC request.
     *
     * @return ResponseInterface HTTP response for a single request.
     */
    private function createSingleResponse(): ResponseInterface
    {
        $stackItem = $this->stack->pop();

        if ($stackItem->isNotificationResponse()) {
            return $this->responseFactory->createResponse(204);
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($stackItem->getResponse(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Creates an HTTP response for multiple JSON-RPC requests.
     *
     * @return ResponseInterface HTTP response for a batch request with multiple responses.
     */
    private function createMultiResponse(): ResponseInterface
    {
        $responseBody = array_filter(array_map(function (JsonRpcResponseItem $item) {
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

    /**
     * Creates a JSON-RPC request from the Dataset.
     *
     * @param Dataset $dataset The dataset containing request data.
     *
     * @return JsonRpcRequest A new JSON-RPC request.
     *
     * @throws MethodNotFoundException If the method is not found.
     */
    private function collectJsonRpcRequest(Dataset $dataset): JsonRpcRequest
    {
        return new JsonRpcRequest(
            id: $dataset->getProperty('id'),
            method: $dataset->getProperty('method'),
            params: $dataset->getProperty('params'),
            isNotification: !$dataset->hasProperty('id')
        );
    }

    /**
     * Decodes the content of an HTTP response.
     *
     * @param ResponseInterface $response The HTTP response to decode.
     *
     * @return mixed Decoded response content.
     */
    private function decodeResponseContent(ResponseInterface $response): mixed
    {
        $rawContent = (string)$response->getBody();
        return $rawContent === '' ? null : json_decode($rawContent, true);
    }

    /**
     * Validates the number of requests in the batch against the configured limit.
     *
     * @param Dataset $dataset The dataset containing the batch content.
     *
     * @throws TooManyRequestsException If the number of requests in the batch exceeds the configured limit.
     */
    private function validateBatchSize(Dataset $dataset): void
    {
        $batchSize = $this->options->getBatchSize();
        if (count($dataset->getContent()) > $batchSize) {
            throw new TooManyRequestsException(content: [
                'actual_count' => count($dataset->getContent()),
                'max_requests' => $batchSize
            ]);
        }
    }
}
