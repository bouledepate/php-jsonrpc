<?php

declare(strict_types=1);

namespace Kernel\Definitions;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class InvalidDefinitionsFormatException extends AbstractException
{
    protected $code = Error::DEFINITIONS_CONFIG_INVALID_FILE;

    public function __construct(string $path)
    {
        $message = "Invalid format in configuration file at '$path'.";
        parent::__construct($this->code, $message);
    }
}