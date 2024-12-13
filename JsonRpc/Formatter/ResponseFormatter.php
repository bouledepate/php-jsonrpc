<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Formatter;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Bouledepate\JsonRpc\Contract\SuccessJsonRpcResponse;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Interfaces\ContentInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @package Bouledepate\JsonRpc\Formatter
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class ResponseFormatter implements FormatterInterface
{
    public function formatError(Throwable $exception): array
    {
        $content = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ContentInterface) {
            $content['data'] = $exception->getContent();
        }

        return $content;
    }

    public function formatResponse(JsonRpcRequest $request, ResponseInterface $response): ResponseInterface
    {
        $content = $this->decodeResponseContent($response);
        $jrpcResponse = new SuccessJsonRpcResponse($request->getId(), $content);

        return $this->writeJsonToResponse($response, $jrpcResponse);
    }

    public function formatInvalidResponse(
        JsonRpcRequest $request,
        ResponseInterface $response,
        Throwable $exception
    ): ResponseInterface {
        $id = $this->getResponseId($request, $exception);
        $jrpcResponse = new ErrorJsonRpcResponse($id, $this->formatError($exception));

        return $this->writeJsonToResponse($response->withStatus(400), $jrpcResponse);
    }

    private function decodeResponseContent(ResponseInterface $response): mixed
    {
        $rawContent = (string)$response->getBody();
        return $rawContent === '' ? null : json_decode($rawContent, true);
    }

    private function writeJsonToResponse(ResponseInterface $response, JsonRpcResponse $data): ResponseInterface
    {
        $response->getBody()->rewind();
        $response->getBody()->write(json_encode($data, flags: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getResponseId(JsonRpcRequest $request, Throwable $exception): string|int|null
    {
        if ($exception instanceof ParseErrorException || $exception instanceof InvalidRequestException) {
            return null;
        }
        return $request->getId();
    }
}
