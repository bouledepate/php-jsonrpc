<?php

declare(strict_types=1);

namespace WoopLeague\Application;

use WoopLeague\Kernel\Config\DependencyProvider;

use function DI\create;

final readonly class Definitions implements DependencyProvider
{
    public function register(): array
    {
        return [
            Example\ExampleInterface::class => create(Example\ExampleProcessor::class)
        ];
    }
}