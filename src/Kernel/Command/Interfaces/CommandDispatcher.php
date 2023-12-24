<?php

declare(strict_types=1);

namespace Kernel\Command\Interfaces;

use Kernel\Command\Contract\CommandRequest;
use Kernel\Command\Contract\CommandResponse;

interface CommandDispatcher
{
    public function dispatch(CommandRequest $commandRequest): CommandResponse;
}