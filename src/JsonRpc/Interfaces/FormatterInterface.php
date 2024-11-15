<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Outlines the methods required for formatting JSON-RPC responses.
 *
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface FormatterInterface
{
    /**
     * Formats a successful JSON-RPC response.
     *
     * @param JsonRpcRequest    $request  The original JSON-RPC request.
     * @param ResponseInterface $response The initial HTTP response object.
     *
     * @return ResponseInterface The formatted HTTP response compliant with JSON-RPC specifications.
     */
    public function formatResponse(JsonRpcRequest $request, ResponseInterface $response): ResponseInterface;

    /**
     * Formats a response for an invalid JSON-RPC request, incorporating exception details.
     *
     * @param JsonRpcRequest    $request   The original JSON-RPC request.
     * @param ResponseInterface $response  The initial HTTP response object.
     * @param Throwable         $exception The exception that caused the request to be invalid.
     *
     * @return ResponseInterface The formatted HTTP response indicating an invalid request.
     */
    public function formatInvalidResponse(
        JsonRpcRequest $request,
        ResponseInterface $response,
        Throwable $exception
    ): ResponseInterface;

    /**
     * Formats an error based on the provided exception.
     *
     * @param Throwable $exception The exception to format into a JSON-RPC error object.
     *
     * @return array An associative array representing the JSON-RPC error object.
     */
    public function formatError(Throwable $exception): array;
}