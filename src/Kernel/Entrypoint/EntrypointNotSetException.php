<?php

declare(strict_types=1);

namespace Kernel\Entrypoint;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class EntrypointNotSetException extends AbstractException
{
    protected $code = Error::ENTRYPOINT_NOT_SET;
    protected $message = "Entrypoint controller is not set!";
    protected string|array|null $detail = "You need to define custom entrypoint or use builtin.";
}