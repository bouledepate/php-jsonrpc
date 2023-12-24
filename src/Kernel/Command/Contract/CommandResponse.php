<?php

declare(strict_types=1);

namespace Kernel\Command\Contract;

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