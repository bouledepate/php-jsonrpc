<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface ErrorHandlerInterface
{
    public function handle(
        JsonRpcRequest $request,
        JsonRpcResponse $response,
        ServerRequestInterface $serverRequest,
        Throwable $exception
    ): ResponseInterface;
}