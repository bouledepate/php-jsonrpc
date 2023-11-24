<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Error\JsonRpc;

final class MethodNotFound extends JsonRpcException
{
    protected $code = ErrorCode::METHOD_NOT_FOUND;
    protected $message = "Method not found.";
}