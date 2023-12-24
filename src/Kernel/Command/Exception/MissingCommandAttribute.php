<?php

declare(strict_types=1);

namespace Kernel\Command\Exception;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;
use Throwable;

final class MissingCommandAttribute extends AbstractException
{
    protected $message = "Missing command attribute.";
    protected $code = Error::MISSING_COMMAND_ATTRIBUTE;

    public function __construct(string $commandHandler)
    {
        $this->detail = "You must provide a `Command` attribute into your command handler! Class: $commandHandler.";
        parent::__construct();
    }
}