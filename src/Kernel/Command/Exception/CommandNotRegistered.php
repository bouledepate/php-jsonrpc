<?php

declare(strict_types=1);

namespace Kernel\Command\Exception;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class CommandNotRegistered extends AbstractException
{
    public $message = "Command not registered.";
    public $code = Error::COMMAND_NOT_REGISTERED;

    public function __construct(string $method)
    {
        $this->detail = "Requested method `$method` is not registered!";
        parent::__construct();
    }
}