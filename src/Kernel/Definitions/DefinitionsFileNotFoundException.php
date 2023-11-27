<?php

declare(strict_types=1);

namespace Kernel\Definitions;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;
use Throwable;

final class DefinitionsFileNotFoundException extends AbstractException
{
    protected $code = Error::DEFINITIONS_CONFIG_NOT_FOUND;

    public function __construct($path)
    {
        $message = "Configuration file not found at '{$path}'.";
        parent::__construct($this->code, $message);
    }
}