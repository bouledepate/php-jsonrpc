<?php

declare(strict_types=1);

namespace Kernel\Exception\JRPC;

use Kernel\Exception\ExceptionCode;

final class InvalidParamsException extends JsonRpcException
{
    protected $code = ExceptionCode::JRPC_INVALID_PARAMS;
    protected $message = "Invalid params.";
}