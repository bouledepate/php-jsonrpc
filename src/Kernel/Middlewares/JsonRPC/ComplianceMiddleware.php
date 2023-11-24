<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Middlewares\JsonRPC;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final readonly class ComplianceMiddleware implements MiddlewareInterface
{
    private const HTTP_NO_CONTENT = 204;

    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
    }

    /**
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (Throwable $exception) {
            // Need to call logger here to log exception.
            $this->isNotification($request) ?: throw $exception;
        }
        return $this->handleResponse($request, $response ?? null);
    }

    private function handleResponse(ServerRequestInterface $request, ?ResponseInterface $response): ResponseInterface
    {
        if ($this->isNotification($request) || $response === null) {
            $response = $this->responseFactory->createResponse(self::HTTP_NO_CONTENT);
        }
        return $this->wrapResponse($request, $response);
    }

    private function wrapResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->isNotification($request)) {
            return $response->withStatus(self::HTTP_NO_CONTENT);
        }

        $responseContent = $this->fetchBodyFrom($response);

        if (!is_array($responseContent) || !isset($responseContent['jsonrpc'])) {
            $responseContent = [
                'jsonrpc' => '2.0',
                'result' => $responseContent,
                'id' => $this->getRequestID($request),
            ];
        }

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($responseContent, JSON_UNESCAPED_SLASHES));

        return $response;
    }

    private function fetchBodyFrom(ResponseInterface $response): mixed
    {
        $content = (string)$response->getBody();
        $response->getBody()->rewind();

        $decodedJson = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decodedJson;
        }

        return $content;
    }

    private function isNotification(ServerRequestInterface $request): bool
    {
        return (bool)$request->getAttribute('is_notification');
    }

    private function getRequestID(ServerRequestInterface $request): string|null
    {
        return $request->getAttribute('request_id') ?? null;
    }
}