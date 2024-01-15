<?php

declare(strict_types=1);

namespace Application\Development;

use Application\Development\Features\CheckEnvironment\CheckEnvironment;
use Application\Development\Features\GetEnvironment\GetEnvironment;
use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Definitions\DependencyProvider;
use function DI\autowire;
use function DI\get;

final readonly class DevelopmentDefinitions implements DependencyProvider
{
    public function register(): array
    {
        return [
            GetEnvironment::class => autowire(),
            CheckEnvironment::class => autowire()->constructor(
                get(ApplicationConfig::class)
            )
        ];
    }
}