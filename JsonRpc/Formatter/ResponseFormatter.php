<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Formatter;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Bouledepate\JsonRpc\Contract\SuccessJsonRpcResponse;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Interfaces\ExceptionContentInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @package Bouledepate\JsonRpc\Formatter
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class ResponseFormatter implements FormatterInterface
{
    /**
     * Formats an error based on the provided exception.
     *
     * @param Throwable $exception The exception to format into a JSON-RPC error object.
     *
     * @return array An associative array representing the JSON-RPC error object.
     */
    public function formatError(Throwable $exception): array
    {
        $content = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ExceptionContentInterface) {
            $content['data'] = $exception->getContent();
        }

        return $content;
    }

    /**
     * Formats a successful JSON-RPC response.
     *
     * @param JsonRpcRequest    $request  The original JSON-RPC request.
     * @param ResponseInterface $response The initial HTTP response object.
     *
     * @return ResponseInterface The formatted HTTP response compliant with JSON-RPC specifications.
     */
    public function formatResponse(JsonRpcRequest $request, ResponseInterface $response): ResponseInterface
    {
        $content = $this->decodeResponseContent($response);
        $jrpcResponse = new SuccessJsonRpcResponse($request->getId(), $content);

        return $this->writeJsonToResponse($response, $jrpcResponse);
    }

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
    ): ResponseInterface {
        $id = $this->getResponseId($request, $exception);
        $jrpcResponse = new ErrorJsonRpcResponse($id, $this->formatError($exception));

        return $this->writeJsonToResponse($response->withStatus(400), $jrpcResponse);
    }

    /**
     * Decodes the JSON content from the HTTP response body.
     *
     * @param ResponseInterface $response The HTTP response containing JSON content.
     *
     * @return array|null The decoded JSON content as an associative array, or null if empty.
     */
    private function decodeResponseContent(ResponseInterface $response): mixed
    {
        $rawContent = (string)$response->getBody();
        return $rawContent === '' ? null : json_decode($rawContent, true);
    }

    /**
     * Writes JSON-encoded data to the HTTP response body.
     *
     * @param ResponseInterface $response The HTTP response to write to.
     * @param JsonRpcResponse   $data     The JSON-RPC response data to encode and write.
     *
     * @return ResponseInterface The HTTP response with the JSON data written to its body.
     */
    private function writeJsonToResponse(ResponseInterface $response, JsonRpcResponse $data): ResponseInterface
    {
        $response->getBody()->rewind();
        $response->getBody()->write(json_encode($data, flags: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Determines the appropriate ID for the JSON-RPC response based on the request and exception.
     *
     * @param JsonRpcRequest $request   The original JSON-RPC request.
     * @param Throwable      $exception The exception that was thrown.
     *
     * @return string|int|null The ID to include in the JSON-RPC response, or null if not applicable.
     */
    private function getResponseId(JsonRpcRequest $request, Throwable $exception): string|int|null
    {
        if ($exception instanceof ParseErrorException || $exception instanceof InvalidRequestException) {
            return null;
        }
        return $request->getId();
    }
}
