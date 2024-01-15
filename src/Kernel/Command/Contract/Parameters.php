<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Contract;

final readonly class Parameters
{
    public function __construct(private array $params = [])
    {
    }

    public function getValue(): array
    {
        return $this->params;
    }

    public function getValueAsString(): string
    {
        return json_encode($this->getValue(), JSON_UNESCAPED_SLASHES);
    }
}