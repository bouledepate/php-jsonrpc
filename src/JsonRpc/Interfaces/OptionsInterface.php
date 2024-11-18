<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

/**
 * Interface for defining configuration options for JSON-RPC processing.
 *
 * Provides methods for retrieving limits on batch size and payload size.
 *
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface OptionsInterface
{
    /**
     * Retrieves the maximum number of requests allowed in a batch.
     *
     * @return int The maximum number of requests per batch.
     */
    public function getBatchSize(): int;

    /**
     * Retrieves the maximum payload size allowed for a request.
     *
     * @return int The maximum payload size in bytes.
     */
    public function getBatchPayloadSize(): int;
}