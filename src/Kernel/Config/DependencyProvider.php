<?php

declare(strict_types=1);

namespace Kernel\Config;

interface DependencyProvider
{
    public function register(): array;
}