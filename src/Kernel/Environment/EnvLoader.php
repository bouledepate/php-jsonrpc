<?php

declare(strict_types=1);

namespace JRPC\Kernel\Environment;

use Dotenv\Dotenv;
use JRPC\Kernel\Configuration\Environment;

final readonly class EnvLoader
{
    public static function loadVariables(): void
    {
        $env = Dotenv::createUnsafeImmutable(dirname(__DIR__, 3));
        $env->load();

        # Required with allowed values list.
        $env->required([Variables::ENVIRONMENT->name])->allowedValues(Environment::values());

        # Required in .env, but can be empty if not necessary.
        $env->required([
            Variables::MIDDLEWARES_CONFIG->name,
            Variables::ROOT_PATH->name
        ]);

        # Required and must be boolean.
        $env->required([
            Variables::DISPLAY_ERROR_DETAILS->name,
            Variables::LOG_ERRORS->name,
            Variables::LOG_ERROR_DETAILS->name,
            Variables::JRPC_USE_DEFAULT_ENTRYPOINT->name,
            Variables::JRPC_BATCH_REQUESTS->name,
            Variables::JRPC_UUID_REQUIRED->name
        ])->isBoolean();

        # Required.
        $env->required([
            Variables::JRPC_ENTRYPOINT->name,
            Variables::COMMANDS_CONFIG->name,
            Variables::DEFINITIONS_CONFIG->name
        ])->notEmpty();
    }
}