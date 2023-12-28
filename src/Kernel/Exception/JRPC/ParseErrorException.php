<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception\JRPC;

use JRPC\Kernel\Exception\ExceptionCode;

final class ParseErrorException extends JsonRpcException
{
    protected $code = ExceptionCode::JRPC_PARSE_ERROR;
    protected $message = "Parse error.";
}