<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\ParseErrorException;
use Bouledepate\JsonRpc\Interfaces\CustomErrorHandlerInterface;
use Bouledepate\JsonRpc\Interfaces\FormatterInterface;
use Bouledepate\JsonRpc\Interfaces\ValidatorInterface;
use Bouledepate\JsonRpc\Interfaces\MethodProviderInterface;
use Bouledepate\JsonRpc\Model\Dataset;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

/**
 * Implements the MiddlewareInterface to handle JSON-RPC requests.
 * Validates requests, invokes appropriate methods, and formats responses.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcMiddleware implements MiddlewareInterface
{
    /**
     * @var FormatterInterface The formatter for JSON-RPC responses.
     */
    private FormatterInterface $formatter;

    /**
     * @var ValidatorInterface The validator for JSON-RPC requests.
     */
    private ValidatorInterface $validator;

    /**
     * @var MethodProviderInterface|null The provider to check for method existence.
     */
    private ?MethodProviderInterface $methodProvider;

    /**
     * @var ResponseFactoryInterface The factory to create HTTP responses.
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * @var CustomErrorHandlerInterface|null The custom error handler.
     */
    private ?CustomErrorHandlerInterface $errorHandler;

    /**
     * Initializes the middleware with necessary dependencies from the container.
     *
     * @param ContainerInterface $container The dependency injection container.
     *
     * @throws NotFoundExceptionInterface If a required service is not found in the container.
     * @throws ContainerExceptionInterface If there is an error retrieving a service from the container.
     */
    public function __construct(
        private readonly ContainerInterface $container
    ) {
        $this->validator = new JsonRpcValidator();
        $this->responseFactory = $this->getResponseFactory();
        $this->methodProvider = $this->getContainerInstance(MethodProviderInterface::class);
        $this->errorHandler = $this->getContainerInstance(CustomErrorHandlerInterface::class);
        $this->formatter = $this->getContainerInstance(FormatterInterface::class, new JsonRpcFormatter());
    }

    /**
     * Processes an incoming server request and produces a response.
     *
     * @param ServerRequestInterface $request The incoming server request.
     * @param RequestHandlerInterface $handler The request handler to delegate to.
     *
     * @return ResponseInterface The HTTP response.
     *
     * @throws ParseErrorException If there is a parsing error in the request.
     * @throws InvalidRequestException If the request is invalid.
     * @throws MethodNotFoundException If the requested method does not exist.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dataset = new Dataset($request);
        $this->validator->validate($dataset);

        $jrpcRequest = new JsonRpcRequest(
            id: $dataset->getProperty('id') ?? false,
            method: $dataset->getProperty('method'),
            params: $dataset->getProperty('params')
        );

        if (!$this->isMethodAvailable($jrpcRequest)) {
            throw new MethodNotFoundException();
        }

        try {
            return $this->processRequest($request, $handler, $jrpcRequest);
        } catch (Throwable $exception) {
            return $this->handleError($request, $jrpcRequest, $exception);
        }
    }

    /**
     * Retrieves an instance from the container or returns a default value.
     *
     * @param string $interface The interface or class name to retrieve.
     * @param mixed|null $default The default value to return if the instance is not found.
     *
     * @return mixed The instance retrieved from the container or the default value.
     *
     * @throws ContainerExceptionInterface If there is an error retrieving the instance.
     * @throws NotFoundExceptionInterface If the interface is not found in the container.
     */
    private function getContainerInstance(string $interface, mixed $default = null): mixed
    {
        if ($this->container->has($interface)) {
            $instance = $this->container->get($interface);
            if ($instance instanceof $interface) {
                return $instance;
            }
        }
        return $default;
    }

    /**
     * Retrieves the ResponseFactoryInterface instance from the container.
     *
     * @return ResponseFactoryInterface The response factory.
     *
     * @throws ContainerExceptionInterface If there is an error retrieving the instance.
     * @throws NotFoundExceptionInterface If the ResponseFactoryInterface is not found in the container.
     */
    private function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->getContainerInstance(ResponseFactoryInterface::class)
            ?? throw new RuntimeException('An instance of ResponseFactoryInterface must be provided.');
    }

    /**
     * Checks if the requested method is available for invocation.
     *
     * @param JsonRpcRequest $jrpcRequest The JSON-RPC request containing the method name.
     *
     * @return bool True if the method exists and is available; otherwise, false.
     */
    private function isMethodAvailable(JsonRpcRequest $jrpcRequest): bool
    {
        return $this->methodProvider && $this->methodProvider->exist($jrpcRequest->getMethod());
    }

    /**
     * Processes the JSON-RPC request by delegating to the request handler and formatting the response.
     *
     * @param ServerRequestInterface $request The incoming server request.
     * @param RequestHandlerInterface $handler The request handler to delegate to.
     * @param JsonRpcRequest $jrpcRequest The JSON-RPC request object.
     *
     * @return ResponseInterface The formatted JSON-RPC response.
     */
    private function processRequest(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        JsonRpcRequest $jrpcRequest
    ): ResponseInterface {
        $request = $request->withAttribute(JsonRpcRequest::class, $jrpcRequest);
        $response = $this->formatter->formatResponse($jrpcRequest, $handler->handle($request));

        if ($jrpcRequest->isNotification()) {
            return $this->responseFactory->createResponse(204);
        }

        return $response;
    }

    /**
     * Handles any exceptions that occur during the processing of the request.
     *
     * @param ServerRequestInterface $request The incoming server request.
     * @param JsonRpcRequest $jrpcRequest The JSON-RPC request object.
     * @param Throwable $exception The exception that was thrown.
     *
     * @return ResponseInterface The HTTP response representing the error.
     */
    private function handleError(
        ServerRequestInterface $request,
        JsonRpcRequest $jrpcRequest,
        Throwable $exception
    ): ResponseInterface {
        if ($jrpcRequest->isNotification()) {
            return $this->responseFactory->createResponse(204);
        }

        if ($this->errorHandler) {
            $id = $exception instanceof ParseErrorException || $exception instanceof InvalidRequestException
                ? null
                : $jrpcRequest->getId();

            $content = $this->formatter->formatError($exception);
            return $this->errorHandler->handle(
                serverRequest: $request,
                jrpcRequest: $jrpcRequest,
                jrpcResponse: new ErrorJsonRpcResponse($id, $content),
                exception: $exception
            );
        }

        return $this->formatter->formatInvalidResponse(
            request: $jrpcRequest,
            response: $this->responseFactory->createResponse(400),
            exception: $exception
        );
    }
}
