<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Exception;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;

final class ConfigurationNotFoundException extends AbstractException
{
    protected $message = "Configuration file not found.";
    protected $code = ExceptionCode::COMMANDS_CONFIG_NOT_FOUND;

    public function __construct(string $path)
    {
        $this->detail = "Configuration file at $path is not exists.";
        parent::__construct();
    }
}