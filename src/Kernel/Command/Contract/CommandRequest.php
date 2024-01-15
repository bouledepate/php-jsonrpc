<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Contract;

use Psr\Http\Message\ServerRequestInterface;

final readonly class CommandRequest
{
    public function __construct(
        private Method                  $method,
        private ?Parameters             $parameters,
        private ?ServerRequestInterface $serverRequest = null
    )
    {
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getParameters(): ?Parameters
    {
        return $this->parameters;
    }

    public function getServerRequest(): ?ServerRequestInterface
    {
        return $this->serverRequest;
    }
}