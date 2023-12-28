<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Interfaces;

use JRPC\Kernel\Command\Contract\CommandRequest;
use JRPC\Kernel\Command\Contract\CommandResponse;

interface CommandDispatcher
{
    public function dispatch(CommandRequest $commandRequest): CommandResponse;
}