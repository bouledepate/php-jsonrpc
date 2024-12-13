<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Handler;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\Core\InternalErrorException;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidParamsException;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Exceptions\Core\ServerErrorException;
use Bouledepate\JsonRpc\Exceptions\PayloadTooLargeException;
use Bouledepate\JsonRpc\Exceptions\TooManyRequestsException;
use Bouledepate\JsonRpc\Interfaces\ContentInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    protected const EXCEPTIONS_MAP = [
        InternalErrorException::class => 400,
        InvalidParamsException::class => 422,
        InvalidRequestException::class => 422,
        MethodNotFoundException::class => 404,
        ParseErrorException::class => 500,
        ServerErrorException::class => 500,
        PayloadTooLargeException::class => 413,
        TooManyRequestsException::class => 429
    ];

    protected readonly ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function handle(?JsonRpcRequest $jrpcRequest, Throwable $exception): ResponseInterface
    {
        $jrpcRequest ??= $this->collectEmptyRequest();

        if ($jrpcRequest->isNotification()) {
            return $this->responseFactory->createResponse(code: 204);
        }

        $jrpcResponse = $this->collectErrorResponse($jrpcRequest, $exception);

        $psrResponse = $this->responseFactory->createResponse(code: $this->defineStatusCode($exception));
        $psrResponse->getBody()->write(json_encode($jrpcResponse, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $psrResponse->withHeader('Content-Type', 'application/json');
    }

    protected function defineStatusCode(Throwable $exception): int
    {
        if (array_key_exists($exception::class, static::EXCEPTIONS_MAP)) {
            return static::EXCEPTIONS_MAP[$exception::class];
        }

        return 400;
    }

    protected function collectErrorResponse(JsonRpcRequest $request, Throwable $exception): ErrorJsonRpcResponse
    {
        $data = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];

        if ($exception instanceof ContentInterface) {
            $data['data'] = $exception->getContent();
        }

        return new ErrorJsonRpcResponse(
            id: $request->getId(),
            error: $data
        );
    }

    protected function collectEmptyRequest(): JsonRpcRequest
    {
        return new JsonRpcRequest(null, null);
    }
}