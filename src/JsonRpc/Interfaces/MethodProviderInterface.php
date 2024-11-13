<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

use Bouledepate\JsonRpc\Model\Method;

/**
 * Specifies how to check for the existence of a method within the JSON-RPC server.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
interface MethodProviderInterface
{
    /**
     * Checks whether the specified method exists and is available for invocation.
     *
     * @param Method $method The method to check for existence.
     *
     * @return bool `true` if the method exists; `false` otherwise.
     */
    public function exist(Method $method): bool;
}