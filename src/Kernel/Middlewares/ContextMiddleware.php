<?php

declare(strict_types=1);

namespace JRPC\Kernel\Middlewares;

use JRPC\Kernel\Exception\JRPC\ParseErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
        $content = (string)$request->getBody();
        $parsedBody = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException();
        }

        return $parsedBody;
    }
}