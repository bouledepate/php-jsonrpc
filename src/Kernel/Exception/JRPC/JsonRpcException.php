<?php

declare(strict_types=1);

namespace Kernel\Exception\JRPC;

use Kernel\Exception\AbstractException;
use Kernel\Exception\ExceptionCode;

abstract class JsonRpcException extends AbstractException
{
    public function __construct(array|string|null $detail = null)
    {
        $this->detail = $detail;
        parent::__construct(
            code: $this->code instanceof ExceptionCode ? $this->code->value : $this->code,
            message: $this->message
        );
    }
}