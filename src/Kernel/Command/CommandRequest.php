<?php

declare(strict_types=1);

namespace Kernel\Command;

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

    public function getParameters(): array
    {
        return $this->parameters;
    }
}