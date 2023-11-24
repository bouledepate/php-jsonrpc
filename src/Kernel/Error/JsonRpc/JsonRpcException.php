<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Error\JsonRpc;

use WoopLeague\Kernel\Error\AbstractException;

abstract class JsonRpcException extends AbstractException
{
    public function __construct(array|string|null $detail = null)
    {
        $this->detail = $detail;
        parent::__construct(
            code: $this->code instanceof ErrorCode ? $this->code->value : $this->code,
            message: $this->message
        );
    }
}