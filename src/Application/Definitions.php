<?php

declare(strict_types=1);

namespace WoopLeague\Application;

use Kernel\Configuration\ApplicationConfig;
use Kernel\Definitions\DependencyProvider;
use WoopLeague\Application\Development\Features\Environment\GetEnvironment;

use function DI\autowire;
use function DI\get;

final readonly class Definitions implements DependencyProvider
{
    public function register(): array
    {
        return [
            GetEnvironment::class => autowire()
                ->constructor(get(ApplicationConfig::class))
        ];
    }
}