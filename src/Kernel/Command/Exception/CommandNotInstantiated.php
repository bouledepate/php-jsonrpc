<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Exception;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;

final class CommandNotInstantiated extends AbstractException
{
    protected $message = "Command handler is not defined.";
    protected $code = ExceptionCode::HANDLER_NOT_INSTANTIATED;

    public function __construct(string $handler)
    {
        $this->detail = "You must define $handler in DI-container.";
        parent::__construct();
    }
}