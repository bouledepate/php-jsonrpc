<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Specifies how custom error handling can be implemented within the JSON-RPC server.
 *
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface CustomErrorHandlerInterface
{
    /**
     * Handles exceptions that occur during the processing of a JSON-RPC request.
     *
     * @param ServerRequestInterface $serverRequest The incoming server request.
     * @param JsonRpcRequest         $jrpcRequest   The JSON-RPC specific request object.
     * @param JsonRpcResponse        $jrpcResponse  The JSON-RPC response object.
     * @param Throwable              $exception     The exception that was thrown during processing.
     *
     * @return ResponseInterface The HTTP response to be sent back to the client, potentially modified to reflect the error.
     */
    public function handle(
        ServerRequestInterface $serverRequest,
        JsonRpcRequest $jrpcRequest,
        JsonRpcResponse $jrpcResponse,
        Throwable $exception
    ): ResponseInterface;
}