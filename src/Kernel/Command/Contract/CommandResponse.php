<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Contract;

final readonly class CommandResponse
{
    public function __construct(private string $data)
    {
    }

    public function getSerializedData(): string
    {
        return $this->data;
    }
}