<?php

namespace Bouledepate\JsonRpc\Handler;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorHandlerInterface
{
    public function handle(?JsonRpcRequest $jrpcRequest, Throwable $exception): ResponseInterface;
}