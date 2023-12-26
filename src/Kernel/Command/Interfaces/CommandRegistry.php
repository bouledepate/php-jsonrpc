<?php

declare(strict_types=1);

namespace Kernel\Command\Interfaces;

use Kernel\Command\Contract\Method;
use Kernel\Exception\JRPC\MethodNotFound;

interface CommandRegistry
{
    public function isCommandExist(Method $method): bool;

    /** @throws MethodNotFound */
    public function fetchHandlerBy(Method $method): string;

    /** @throws MethodNotFound */
    public function isDtoRequiredFor(Method $method): bool;

    /**@throws MethodNotFound */
    public function fetchDtoBy(Method $method): ?string;
}