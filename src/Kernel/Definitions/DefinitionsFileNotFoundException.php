<?php

declare(strict_types=1);

namespace JRPC\Kernel\Definitions;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;
use Throwable;

final class DefinitionsFileNotFoundException extends AbstractException
{
    protected $code = ExceptionCode::DEFINITIONS_CONFIG_NOT_FOUND;

    public function __construct($path)
    {
        $message = "Configuration file not found at '{$path}'.";
        parent::__construct($this->code, $message);
    }
}