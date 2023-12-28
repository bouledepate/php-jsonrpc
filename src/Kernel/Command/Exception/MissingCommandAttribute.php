<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Exception;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;
use Throwable;

final class MissingCommandAttribute extends AbstractException
{
    protected $message = "Missing command attribute.";
    protected $code = ExceptionCode::MISSING_COMMAND_ATTRIBUTE;

    public function __construct(string $commandHandler)
    {
        $this->detail = "You must provide a `Command` attribute into your command handler! Class: $commandHandler.";
        parent::__construct();
    }
}