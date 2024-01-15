<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Interfaces;

use JRPC\Kernel\Exception\JRPC\MethodNotFound;

interface CommandRegistry
{
    /** @throws MethodNotFound */
    public function fetchCommandBy(string $method): string;

    /** @throws MethodNotFound */
    public function isDtoRequiredFor(string $method): bool;

    /**@throws MethodNotFound */
    public function fetchDtoBy(string $method): ?string;
}