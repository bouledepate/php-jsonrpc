<?php

declare(strict_types=1);

namespace Kernel\Command\Interfaces;

use Kernel\Command\Contract\Method;
use Kernel\Command\Exception\CommandNotRegistered;

interface CommandRegistry
{
    public function isCommandExist(Method $method): bool;

    /** @throws CommandNotRegistered */
    public function fetchHandlerBy(Method $method): string;

    /** @throws CommandNotRegistered */
    public function isDtoRequiredFor(Method $method): bool;

    /**@throws CommandNotRegistered */
    public function fetchDtoBy(Method $method): ?string;
}