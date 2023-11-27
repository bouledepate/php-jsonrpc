<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class CommandsFileInvalidFormatException extends AbstractException
{
    protected $code = Error::COMMANDS_CONFIG_INVALID_FILE;

    public function __construct(string $path)
    {
        $message = "Invalid format in configuration file at '$path'.";
        parent::__construct($this->code, $message);
    }
}