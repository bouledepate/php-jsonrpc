<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Config;

/**
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface OptionsInterface
{
    public function getBatchSize(): int;
    public function getPayloadSize(): int;
}