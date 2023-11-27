<?php

declare(strict_types=1);

namespace Kernel\Error\JsonRpc;

final class InvalidParamsException extends JsonRpcException
{
    protected $code = ErrorCode::INVALID_PARAMS;
    protected $message = "Invalid params.";
}