<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Config\DefaultJsonRpcOptions;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\PayloadTooLargeException;
use Bouledepate\JsonRpc\Formatter\FormatterInterface;
use Bouledepate\JsonRpc\Formatter\ResponseFormatter;
use Bouledepate\JsonRpc\Interfaces\MethodProviderInterface;
use Bouledepate\JsonRpc\Interfaces\OptionsInterface;
use Bouledepate\JsonRpc\Validator\RequestValidator;
use Bouledepate\JsonRpc\Validator\ValidatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * @package Bouledepate\JsonRpc
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
abstract class JsonRpcBaseMiddleware extends DefaultMiddleware
{
    /**
     * Tracks whether any JsonRpc middleware has been registered to prevent duplicates.
     *
     * @var bool
     */
    private static bool $middlewareRegistered = false;

    /**
     * Formatter for JSON-RPC responses.
     *
     * @var FormatterInterface
     */
    protected FormatterInterface $formatter;

    /**
     * Validator for JSON-RPC requests.
     *
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * Provider to check for the existence of methods in JSON-RPC requests.
     *
     * @var MethodProviderInterface|null
     */
    protected ?MethodProviderInterface $methodProvider;

    /**
     * Factory for creating HTTP responses.
     *
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * Options for configuring batch processing.
     *
     * @var OptionsInterface
     */
    protected readonly OptionsInterface $options;

    /**
     * Initializes the middleware with dependencies provided by a container.
     *
     * Ensures that only one instance of a JsonRpc middleware can be registered
     * at a time. Throws a RuntimeException if another middleware of the same type
     * is already registered.
     *
     * @param ContainerInterface $container Dependency injection container.
     *
     * @throws RuntimeException If another JsonRpc middleware is already registered.
     * @throws NotFoundExceptionInterface If a required service is not found in the container.
     * @throws ContainerExceptionInterface If there is an error retrieving a service from the container.
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        if (self::$middlewareRegistered) {
            throw new RuntimeException(static::class . ' cannot be used together with another JsonRpc middleware.');
        }

        self::$middlewareRegistered = true;

        $this->validator = new RequestValidator();
        $this->responseFactory = $this->getResponseFactory();
        $this->methodProvider = $this->getContainerInstance(MethodProviderInterface::class);
        $this->formatter = $this->getContainerInstance(FormatterInterface::class, new ResponseFormatter());
        $this->options = $this->getContainerInstance(OptionsInterface::class, new DefaultJsonRpcOptions());
    }

    /**
     * Determines whether a given JSON-RPC method is available for invocation.
     *
     * This method checks the existence of the requested method in the
     * MethodProviderInterface, if it is configured.
     *
     * @param JsonRpcRequest $jrpcRequest The JSON-RPC request containing the method name.
     *
     * @return bool True if the method exists and is available; false otherwise.
     */
    protected function isMethodAvailable(JsonRpcRequest $jrpcRequest): bool
    {
        return $this->methodProvider && $this->methodProvider->exist($jrpcRequest->getMethod());
    }


    /**
     * Validates the payload size of the batch request against the configured limit.
     *
     * @param ServerRequestInterface $request The incoming HTTP request.
     *
     * @throws PayloadTooLargeException If the payload size exceeds the configured limit.
     */
    protected function validatePayloadSize(ServerRequestInterface $request): void
    {
        $payloadSize = $this->options->getBatchPayloadSize();
        $actualPayloadSize = strlen($request->getBody()->getContents());

        if ($actualPayloadSize > $payloadSize) {
            throw new PayloadTooLargeException(content: [
                'actual_size' => $actualPayloadSize,
                'max_payload_size' => $payloadSize
            ]);
        }
    }
}