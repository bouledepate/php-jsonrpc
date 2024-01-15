<?php

declare(strict_types=1);

namespace Application\Development\Features\GetEnvironment;

final readonly class GetEnvironmentResponse
{
    public function __construct(private string $environment)
    {
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}