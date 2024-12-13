<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

/**
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface ContentInterface
{
    public function getContent(): mixed;
}