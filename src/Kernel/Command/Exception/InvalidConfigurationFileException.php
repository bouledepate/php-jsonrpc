<?php

declare(strict_types=1);

namespace Kernel\Command\Exception;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class InvalidConfigurationFileException extends AbstractException
{
    protected $message = "Invalid configuration file.";
    protected $code = Error::COMMANDS_CONFIG_INVALID_FILE;

    public function __construct(string $path)
    {
        $this->detail = "Uploaded file at $path is not valid configuration file.";
        parent::__construct();
    }
}