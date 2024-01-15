<?php

declare(strict_types=1);

namespace Application\Development\Components;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Random\Randomizer;

final readonly class TokenMiddleware implements MiddlewareInterface
{
    public const TOKEN_ATTRIBUTE = 'token';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle(
            $request->withAttribute(
                TokenMiddleware::TOKEN_ATTRIBUTE,
                $this->generateToken()
            )
        );
    }

    private function generateToken(): string
    {
        $generator = new Randomizer();
        return bin2hex($generator->getBytes(16));
    }
}