<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

use Bouledepate\JsonRpc\Model\Method;

/**
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface MethodProviderInterface
{
    public function exist(Method $method): bool;
}