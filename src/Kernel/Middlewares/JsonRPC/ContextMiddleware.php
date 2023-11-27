<?php

declare(strict_types=1);

namespace Kernel\Middlewares\JsonRPC;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Kernel\Error\JsonRpc\ParseErrorException;

final readonly class ContextMiddleware implements MiddlewareInterface
{
    /**
     * @throws ParseErrorException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->injectAttributes($request);
        return $handler->handle($request);
    }

    /**
     * @throws ParseErrorException
     */
    private function injectAttributes(ServerRequestInterface $request): ServerRequestInterface
    {
        $requestBody = $this->fetchDataFrom($request);

        $isNotification = !isset($requestBody['id']);
        $requestID = $requestBody['id'] ?? null;

        return $request
            ->withAttribute('is_notification', $isNotification)
            ->withAttribute('request_id', $requestID);
    }

    /**
     * @throws ParseErrorException
     */
    private function fetchDataFrom(ServerRequestInterface $request): array
    {
        $requestBody = json_decode((string)$request->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException();
        }

        return $requestBody;
    }
}