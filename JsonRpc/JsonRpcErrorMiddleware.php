<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Formatter\FormatterInterface;
use Bouledepate\JsonRpc\Formatter\ResponseFormatter;
use Bouledepate\JsonRpc\Interfaces\CustomErrorHandlerInterface;
use Bouledepate\JsonRpc\Model\Dataset;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * @package Bouledepate\JsonRpc
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcErrorMiddleware extends DefaultMiddleware implements MiddlewareInterface
{
    /**
     * The formatter for JSON-RPC responses.
     *
     * @var FormatterInterface
     */
    private FormatterInterface $formatter;

    /**
     * The custom error handler.
     *
     * @var CustomErrorHandlerInterface|null
     */
    private ?CustomErrorHandlerInterface $errorHandler;

    /**
     * The factory to create HTTP responses.
     *
     * @var ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * Initializes the middleware with necessary dependencies from the container.
     *
     * @param ContainerInterface $container The dependency injection container.
     *
     * @throws ContainerExceptionInterface If there is an error while retrieving dependencies.
     * @throws NotFoundExceptionInterface If a dependency is not found in the container.
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->responseFactory = $this->getResponseFactory();
        $this->errorHandler = $this->getContainerInstance(CustomErrorHandlerInterface::class);
        $this->formatter = $this->getContainerInstance(FormatterInterface::class, new ResponseFormatter());
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface  $request The incoming server request.
     * @param RequestHandlerInterface $handler The request handler.
     *
     * @return ResponseInterface The response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            return $this->handleError($request, $exception);
        }
    }

    /**
     * Handle an exception and convert it into a JSON-RPC error response.
     *
     * @param ServerRequestInterface $request   The server request that caused the exception.
     * @param Throwable              $exception The thrown exception.
     *
     * @return ResponseInterface The JSON-RPC error response.
     */
    private function handleError(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        $jrpcRequest = $this->collectRequest($request, $exception);

        if ($jrpcRequest->isNotification()) {
            return $this->responseFactory->createResponse(204);
        }

        $response = $this->responseFactory->createResponse(code: 400);
        $jrpcResponse = $this->collectInvalidResponse($jrpcRequest, $exception);

        if ($this->errorHandler !== null) {
            $exception = $this->errorHandler->handle($request, $jrpcRequest, $jrpcResponse, $exception);
        }

        return $this->formatter->formatInvalidResponse($jrpcRequest, $response, $exception);
    }

    /**
     * Collect the JSON-RPC request from the server request and exception.
     *
     * @param ServerRequestInterface $request   The incoming server request.
     * @param Throwable              $exception The exception that was thrown.
     *
     * @return JsonRpcRequest The constructed JSON-RPC request.
     */
    private function collectRequest(ServerRequestInterface $request, Throwable $exception): JsonRpcRequest
    {
        if ($exception instanceof ParseErrorException || $exception instanceof InvalidRequestException) {
            return new JsonRpcRequest(null, null, null);
        }

        try {
            $dataset = new Dataset($request);

            $id = $dataset->getProperty('id');
            $method = $dataset->getProperty('method');
            $params = $dataset->getProperty('params');
            $isNotification = !$dataset->hasProperty('id');

            return new JsonRpcRequest($id, $method, $params, $isNotification);
        } catch (MethodNotFoundException|ParseErrorException) {
            return new JsonRpcRequest(null, null, null);
        }
    }

    /**
     * Collect an invalid JSON-RPC response based on the request and exception.
     *
     * @param JsonRpcRequest $request   The JSON-RPC request.
     * @param Throwable      $exception The exception that was thrown.
     *
     * @return JsonRpcResponse The constructed invalid JSON-RPC response.
     */
    private function collectInvalidResponse(JsonRpcRequest $request, Throwable $exception): JsonRpcResponse
    {
        $errorContent = $this->formatter->formatError($exception);
        return new ErrorJsonRpcResponse($request->getId(), $errorContent);
    }
}