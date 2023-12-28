<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Contract;

final readonly class CommandResponse
{
    public function __construct(private string $serializedData)
    {
    }

    public function getSerializedData(): string
    {
        return $this->serializedData;
    }
}