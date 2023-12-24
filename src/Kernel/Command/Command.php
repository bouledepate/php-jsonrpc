<?php

declare(strict_types=1);

namespace Kernel\Command;

use Attribute;

#[Attribute(flags: Attribute::TARGET_CLASS)]
final readonly class Command
{
    private string $handler;

    public function __construct(
        private string  $name,
        private ?string $dto = null,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDTO(): ?string
    {
        return $this->dto;
    }

    public function getHandler(): string
    {
        return $this->handler;
    }

    public function setHandler(string $handler): void
    {
        $this->handler = $handler;
    }
}