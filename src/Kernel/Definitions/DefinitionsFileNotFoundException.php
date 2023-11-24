<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Definitions;

use RuntimeException;
use Throwable;

final class DefinitionsFileNotFoundException extends RuntimeException
{
    public function __construct($path, $code = 0, Throwable $previous = null)
    {
        $message = "Configuration file not found at '{$path}'.";
        parent::__construct($message, $code, $previous);
    }
}