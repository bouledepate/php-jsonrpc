<?php

declare(strict_types=1);

namespace Kernel\Error\JsonRpc;

final class ParseErrorException extends JsonRpcException
{
    protected $code = ErrorCode::PARSE_ERROR;
    protected $message = "Parse error.";
}