<?php

declare(strict_types=1);

namespace Kernel\Definitions;

use Kernel\Exception\AbstractException;
use Kernel\Exception\ExceptionCode;
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