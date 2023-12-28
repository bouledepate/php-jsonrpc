<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Interfaces;

use JRPC\Kernel\Exception\JRPC\MethodNotFound;

interface CommandRegistry
{
    public function isCommandExist(string $method): bool;

    /** @throws MethodNotFound */
    public function fetchHandlerBy(string $method): string;

    /** @throws MethodNotFound */
    public function isDtoRequiredFor(string $method): bool;

    /**@throws MethodNotFound */
    public function fetchDtoBy(string $method): ?string;
}