<?php

declare(strict_types=1);

namespace Kernel\Environment;

enum Variables
{
    case ENVIRONMENT;
    case DISPLAY_ERROR_DETAILS;
    case LOG_ERRORS;
    case LOG_ERROR_DETAILS;
    case ENTRYPOINT;
    case USE_DEFAULT_ENTRYPOINT;
    case BATCH_REQUESTS;
    case COMMANDS_CONFIG;
    case DEFINITIONS_CONFIG;
}