<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Config;

/**
 * @package Bouledepate\JsonRpc\Config
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class JsonRpcOptions implements OptionsInterface
{
    public function getBatchSize(): int
    {
        return 20;
    }

    public function getPayloadSize(): int
    {
        return 1024 * 1024;
    }
}
