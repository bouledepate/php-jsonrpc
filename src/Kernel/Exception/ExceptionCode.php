<?php

declare(strict_types=1);

namespace Kernel\Exception;

enum ExceptionCode: int
{
    // JRPC error codes.
    case JRPC_PARSE_ERROR = -32700;
    case JRPC_INVALID_REQUEST = -32600;
    case JRPC_METHOD_NOT_FOUND = -32601;
    case JRPC_INVALID_PARAMS = -32602;
    case JRPC_INTERNAL_ERROR = -32603;

    // System error codes.
    case UNEXPECTED = -27000;
    case ENTRYPOINT_NOT_SET = -27001;
    case COMMANDS_CONFIG_NOT_FOUND = -27002;
    case COMMANDS_CONFIG_INVALID_FILE = -27003;
    case MISSING_COMMAND_ATTRIBUTE = -27004;
    case HANDLER_NOT_INSTANTIATED = -27005;
    case DEFINITIONS_CONFIG_NOT_FOUND = -27006;
    case DEFINITIONS_CONFIG_INVALID_FILE = -27007;
    case VALIDATION_FAILED = -27100;
}