<?php

declare(strict_types=1);

namespace Application\Development\Features\CheckEnvironment;

final readonly class CheckEnvironmentResponse
{
    public function __construct(
        private ?string $token,
        private bool    $isValid,
    )
    {
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}