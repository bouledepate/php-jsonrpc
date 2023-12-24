<?php

declare(strict_types=1);

namespace Kernel\Command\Exception;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class CommandHandlerNotInstantiated extends AbstractException
{
    protected $message = "Command handler is not defined.";
    protected $code = Error::HANDLER_NOT_INSTANTIATED;

    public function __construct(string $handler)
    {
        $this->detail = "You must define $handler in DI-container.";
        parent::__construct();
    }
}