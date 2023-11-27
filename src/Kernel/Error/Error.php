<?php

declare(strict_types=1);

namespace Kernel\Error;

enum Error: int
{
    case UNEXPECTED = -1;
    case VALIDATION_FAILED = -2;
    case TYPECAST_FAILED = -3;
    case ENTRYPOINT_NOT_SET = -27000;
    case COMMANDS_CONFIG_NOT_FOUND = -27001;
    case COMMANDS_CONFIG_INVALID_FILE = -27002;
    case DEFINITIONS_CONFIG_NOT_FOUND = -27003;
    case DEFINITIONS_CONFIG_INVALID_FILE = -27004;
}