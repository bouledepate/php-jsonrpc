<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Entrypoint;

use RuntimeException;
use Throwable;

final class EntrypointNotSetException extends RuntimeException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = "Entrypoint controller is not set!";
        parent::__construct($message, $code, $previous);
    }
}