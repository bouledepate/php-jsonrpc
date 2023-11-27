<?php

declare(strict_types=1);

namespace Kernel\Helpers;

use Dotenv\Dotenv;
use Kernel\Config\Environment;

final readonly class EnvironmentHelper
{
    public static function loadVariables(): void
    {
        $env = Dotenv::createUnsafeImmutable(dirname(__DIR__, 3));
        $env->load();

        # Required variables must be set.
        $env->required(['ENVIRONMENT'])->allowedValues(Environment::values());
        $env->required(['DISPLAY_ERROR_DETAILS', 'LOG_ERRORS', 'LOG_ERROR_DETAILS'])
            ->isBoolean();

        # Application settings.
        $env->required(['ENTRYPOINT', 'COMMANDS_CONFIG', 'DEFINITIONS_CONFIG']);
        $env->required(['USE_DEFAULT_ENTRYPOINT', 'BATCH_REQUESTS'])->isBoolean();
    }
}