<?php

declare(strict_types=1);

namespace Kernel\Exception\JRPC;

use Kernel\Exception\ExceptionCode;

final class InternalErrorException extends JsonRpcException
{
    protected $code = ExceptionCode::JRPC_INTERNAL_ERROR;
    protected $message = "Internal error.";
}