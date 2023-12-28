<?php

declare(strict_types=1);

namespace JRPC\Kernel\Middlewares;

use JRPC\Kernel\Exception\JRPC\InvalidParamsException;
use JRPC\Kernel\Exception\JRPC\InvalidRequestException;
use JRPC\Kernel\Exception\JRPC\ParseErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;

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