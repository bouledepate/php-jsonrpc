<?php

declare(strict_types=1);

namespace Kernel\Middlewares\JsonRPC;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use Kernel\Error\JsonRpc\InvalidParamsException;
use Kernel\Error\JsonRpc\InvalidRequestException;
use Kernel\Error\JsonRpc\ParseErrorException;

final readonly class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestValidator $validator)
    {
    }

    /**
     * @throws InvalidParamsException
     * @throws InvalidRequestException
     * @throws ParseErrorException
     * @throws ReflectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->validator->validate($request);
        return $handler->handle($request);
    }
}