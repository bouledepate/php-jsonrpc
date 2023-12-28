<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Exception;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;

final class InvalidConfigurationFileException extends AbstractException
{
    protected $message = "Invalid configuration file.";
    protected $code = ExceptionCode::COMMANDS_CONFIG_INVALID_FILE;

    public function __construct(string $path)
    {
        $this->detail = "Uploaded file at $path is not valid configuration file.";
        parent::__construct();
    }
}