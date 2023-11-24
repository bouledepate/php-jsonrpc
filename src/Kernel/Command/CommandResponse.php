<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Command;

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