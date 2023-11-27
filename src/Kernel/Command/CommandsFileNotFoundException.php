<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class CommandsFileNotFoundException extends AbstractException
{
    protected $code = Error::COMMANDS_CONFIG_NOT_FOUND;

    public function __construct($path)
    {
        $message = "Configuration file not found at '{$path}'.";
        parent::__construct($this->code, $message);
    }
}