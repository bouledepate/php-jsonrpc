<?php

declare(strict_types=1);

namespace Kernel\Command\Exception;

use Kernel\Exception\AbstractException;
use Kernel\Exception\ExceptionCode;

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