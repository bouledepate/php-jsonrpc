<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\PayloadTooLargeException;
use Bouledepate\JsonRpc\Formatter\FormatterInterface;
use Bouledepate\JsonRpc\Formatter\ResponseFormatter;
use Bouledepate\JsonRpc\Handler\ErrorHandlerInterface;
use Bouledepate\JsonRpc\Interfaces\MethodProviderInterface;
use Bouledepate\JsonRpc\Validator\RequestValidator;
use Bouledepate\JsonRpc\Validator\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * @package Bouledepate\JsonRpc
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
abstract class JsonRpcBaseMiddleware extends DefaultMiddleware
{
    protected FormatterInterface $formatter;

    protected ValidatorInterface $validator;

    protected ?MethodProviderInterface $methodProvider;

    protected ResponseFactoryInterface $responseFactory;

    protected ErrorHandlerInterface $errorHandler;

    private static bool $middlewareRegistered = false;

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
        $this->errorHandler = $this->getContainerInstance(ErrorHandlerInterface::class);
    }

    protected function isMethodAvailable(JsonRpcRequest $jrpcRequest): bool
    {
        return $this->methodProvider && $this->methodProvider->exist($jrpcRequest->getMethod());
    }

    protected function validatePayloadSize(ServerRequestInterface $request): void
    {
        $payloadSize = $this->options->getPayloadSize();
        $actualPayloadSize = strlen($request->getBody()->getContents());

        if ($actualPayloadSize > $payloadSize) {
            throw new PayloadTooLargeException(content: [
                'request_payload' => $actualPayloadSize,
                'allowed_payload' => $payloadSize
            ]);
        }
    }
}