<?php

declare(strict_types=1);

namespace Kernel\Command;

final readonly class CommandResponse
{
    public function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }
}