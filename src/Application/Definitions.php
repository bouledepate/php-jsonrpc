<?php

declare(strict_types=1);

namespace WoopLeague\Application;

use WoopLeague\Kernel\Command\CommandProvider;
use WoopLeague\Kernel\Config\DependencyProvider;
use WoopLeague\Kernel\Config\EntrypointController;

use function DI\autowire;

final readonly class Definitions implements DependencyProvider, CommandProvider
{
    public function register(): array
    {
        return [
            EntrypointController::class => autowire(Entrypoint::class)
        ];
    }

    public function commands(): array
    {
        return [];
    }
}