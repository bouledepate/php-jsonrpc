<?php

declare(strict_types=1);

namespace Kernel\Entrypoint;

use Kernel\Exception\AbstractException;
use Kernel\Exception\ExceptionCode;

final class EntrypointNotSetException extends AbstractException
{
    protected $code = ExceptionCode::ENTRYPOINT_NOT_SET;
    protected $message = "Entrypoint controller is not set!";
    protected string|array|null $detail = "You need to define custom entrypoint or use builtin.";
}