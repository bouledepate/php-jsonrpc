<?php

declare(strict_types=1);

namespace Application;

use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Definitions\DependencyProvider;
use Application\Development\Features\Environment\GetEnvironment;

use function DI\autowire;
use function DI\get;

final readonly class Definitions implements DependencyProvider
{
    public function register(): array
    {
        return [
            GetEnvironment::class => autowire()->constructor(
                get(ApplicationConfig::class)
            )
        ];
    }
}