<?php

declare(strict_types=1);

namespace Kernel\Definitions;

interface DependencyProvider
{
    public function register(): array;
}