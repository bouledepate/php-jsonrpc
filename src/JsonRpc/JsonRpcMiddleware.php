<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\ParseErrorException;
use Bouledepate\JsonRpc\Interfaces\FormatterInterface;
use Bouledepate\JsonRpc\Interfaces\MethodProviderInterface;
use Bouledepate\JsonRpc\Interfaces\ValidatorInterface;
use Bouledepate\JsonRpc\Model\Dataset;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @package Bouledepate\JsonRpc
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcMiddleware extends DefaultMiddleware
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
     * Initializes the middleware with necessary dependencies from the container.
     *
     * @param ContainerInterface $container The dependency injection container.
     *
     * @throws NotFoundExceptionInterface If a required service is not found in the container.
     * @throws ContainerExceptionInterface If there is an error retrieving a service from the container.
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->validator = new JsonRpcValidator();
        $this->responseFactory = $this->getResponseFactory();
        $this->methodProvider = $this->getContainerInstance(MethodProviderInterface::class);
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
            id: $dataset->getProperty('id'),
            method: $dataset->getProperty('method'),
            params: $dataset->getProperty('params'),
            isNotification: !$dataset->hasProperty('id')
        );

        if (!$this->isMethodAvailable($jrpcRequest)) {
            throw new MethodNotFoundException();
        }

        return $this->processRequest($request, $handler, $jrpcRequest);
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
}
