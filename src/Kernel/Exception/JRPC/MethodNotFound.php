<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception\JRPC;

use JRPC\Kernel\Exception\ExceptionCode;

final class MethodNotFound extends JsonRpcException
{
    protected $code = ExceptionCode::JRPC_METHOD_NOT_FOUND;
    protected $message = "Method not found.";
}