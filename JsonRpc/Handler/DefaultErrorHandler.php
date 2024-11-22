<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Handler;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Bouledepate\JsonRpc\Interfaces\ErrorHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class DefaultErrorHandler implements ErrorHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->responseFactory = $factory;
    }

    public function handle(JsonRpcRequest $request,
        JsonRpcResponse $response,
        ServerRequestInterface $serverRequest,
        Throwable $exception
    ): ResponseInterface {

    }
}