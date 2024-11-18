<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Config;

use Bouledepate\JsonRpc\Interfaces\OptionsInterface;

/**
 * Default implementation of OptionsInterface for JSON-RPC configuration.
 *
 * Provides default values for batch size and payload size limits in JSON-RPC requests.
 *
 * @package Bouledepate\JsonRpc\Config
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
class DefaultJsonRpcOptions implements OptionsInterface
{
    /**
     * Retrieves the maximum number of requests allowed in a batch.
     *
     * @return int The maximum number of requests per batch. Default: 20.
     */
    public function getBatchSize(): int
    {
        return 20;
    }

    /**
     * Retrieves the maximum payload size allowed for a request.
     *
     * @return int The maximum payload size in bytes. Default: 1 MB.
     */
    public function getBatchPayloadSize(): int
    {
        return 1024 * 1024;
    }
}
