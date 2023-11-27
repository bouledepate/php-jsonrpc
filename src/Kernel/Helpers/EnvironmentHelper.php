<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Helpers;

use Dotenv\Dotenv;
use WoopLeague\Kernel\Config\Environment;

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
        $env->required(['ENTRYPOINT']);
        $env->required(['USE_DEFAULT_ENTRYPOINT'])->isBoolean();
    }
}