<?php

declare(strict_types=1);

namespace JRPC\Kernel\Definitions;

interface DependencyProvider
{
    public function register(): array;
}