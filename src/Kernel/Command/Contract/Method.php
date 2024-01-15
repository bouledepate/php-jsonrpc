<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Contract;

final readonly class Method
{
    public function __construct(private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}