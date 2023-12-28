<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception\JRPC;

use JRPC\Kernel\Exception\ExceptionCode;

final class InvalidRequestException extends JsonRpcException
{
    protected $code = ExceptionCode::JRPC_INVALID_REQUEST;
    protected $message = "Invalid request.";
}