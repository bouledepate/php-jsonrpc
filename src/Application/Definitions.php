<?php

declare(strict_types=1);

namespace WoopLeague\Application;

use Kernel\Definitions\DependencyProvider;

final readonly class Definitions implements DependencyProvider
{
    public function register(): array
    {
        return [];
    }
}