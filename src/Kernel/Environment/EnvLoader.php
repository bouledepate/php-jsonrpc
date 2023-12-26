<?php

declare(strict_types=1);

namespace Kernel\Environment;

use Dotenv\Dotenv;
use Kernel\Configuration\Environment;

final readonly class EnvLoader
{
    public static function loadVariables(): void
    {
        $env = Dotenv::createUnsafeImmutable(dirname(__DIR__, 3));
        $env->load();

        # Required variables must be set.
        $env->required([Variables::ENVIRONMENT->name])->allowedValues(Environment::values());

        $env->required([
            Variables::DISPLAY_ERROR_DETAILS->name,
            Variables::LOG_ERRORS->name,
            Variables::LOG_ERROR_DETAILS->name
        ])->isBoolean();

        # Application settings.
        $env->required([
            Variables::ENTRYPOINT->name,
            Variables::COMMANDS_CONFIG->name,
            Variables::DEFINITIONS_CONFIG->name
        ])->notEmpty();

        $env->required([
            Variables::USE_DEFAULT_ENTRYPOINT->name,
            Variables::BATCH_REQUESTS->name
        ])->isBoolean();
    }
}