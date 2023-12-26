<?php

declare(strict_types=1);

namespace Kernel\Exception\JRPC;

use Kernel\Exception\ExceptionCode;

final class InvalidRequestException extends JsonRpcException
{
    protected $code = ExceptionCode::JRPC_INVALID_REQUEST;
    protected $message = "Invalid request.";
}