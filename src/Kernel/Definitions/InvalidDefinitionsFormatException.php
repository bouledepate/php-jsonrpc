<?php

declare(strict_types=1);

namespace JRPC\Kernel\Definitions;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;

final class InvalidDefinitionsFormatException extends AbstractException
{
    protected $code = ExceptionCode::DEFINITIONS_CONFIG_INVALID_FILE;

    public function __construct(string $path)
    {
        $message = "Invalid format in configuration file at '$path'.";
        parent::__construct($this->code, $message);
    }
}