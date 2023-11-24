<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Error\JsonRpc;

final class InternalErrorException extends JsonRpcException
{
    protected $code = ErrorCode::INTERNAL_ERROR;
    protected $message = "Internal error.";
}