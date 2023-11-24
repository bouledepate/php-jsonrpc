<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Command;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Command
{
    public function __construct(
        private string  $name,
        private ?string $dtoClass = null,
        private bool    $dtoRequired = true
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDtoClass(): ?string
    {
        return $this->dtoClass;
    }

    public function isDtoRequired(): bool
    {
        return $this->dtoRequired;
    }
}