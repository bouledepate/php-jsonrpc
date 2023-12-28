<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Contract;

final readonly class CommandRequest
{
    public function __construct(
        private string $method,
        private array  $parameters = []
    )
    {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParameters(): string
    {
        return json_encode($this->parameters, JSON_UNESCAPED_SLASHES);
    }

    public function getParametersAsArray(): array
    {
        return $this->parameters;
    }
}