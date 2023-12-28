<?php

declare(strict_types=1);

namespace JRPC\Kernel\Entrypoint;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;

final class EntrypointNotSetException extends AbstractException
{
    protected $code = ExceptionCode::ENTRYPOINT_NOT_SET;
    protected $message = "Entrypoint controller is not set!";
    protected string|array|null $detail = "You need to define custom entrypoint or use builtin.";
}