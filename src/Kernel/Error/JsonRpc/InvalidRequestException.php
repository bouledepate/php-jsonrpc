<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Error\JsonRpc;

final class InvalidRequestException extends JsonRpcException
{
    protected $code = ErrorCode::INVALID_REQUEST;
    protected $message = "Invalid request.";
}