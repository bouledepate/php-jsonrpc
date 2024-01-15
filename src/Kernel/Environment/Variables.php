<?php

declare(strict_types=1);

namespace JRPC\Kernel\Environment;

enum Variables
{
    case ENVIRONMENT;
    case ROOT_PATH;
    case DISPLAY_ERROR_DETAILS;
    case LOG_ERRORS;
    case LOG_ERROR_DETAILS;
    case COMMANDS_CONFIG;
    case DEFINITIONS_CONFIG;
    case MIDDLEWARES_CONFIG;
    case JRPC_ENTRYPOINT;
    case JRPC_USE_DEFAULT_ENTRYPOINT;
    case JRPC_BATCH_REQUESTS;
    case JRPC_UUID_REQUIRED;
}