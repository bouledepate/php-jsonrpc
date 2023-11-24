<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Config;

interface DependencyProvider
{
    public function register(): array;
}