<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Formatter;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @package Bouledepate\JsonRpc\Formatter
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface FormatterInterface
{

    public function formatResponse(JsonRpcRequest $request, ResponseInterface $response): ResponseInterface;

    public function formatInvalidResponse(
        JsonRpcRequest $request,
        ResponseInterface $response,
        Throwable $exception
    ): ResponseInterface;

    public function formatError(Throwable $exception): array;
}