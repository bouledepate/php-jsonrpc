<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Middlewares\JsonRPC;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use WoopLeague\Kernel\Error\JsonRpc\InvalidParamsException;
use WoopLeague\Kernel\Error\JsonRpc\InvalidRequestException;
use WoopLeague\Kernel\Error\JsonRpc\ParseErrorException;

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