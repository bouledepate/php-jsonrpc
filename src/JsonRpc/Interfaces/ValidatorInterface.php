<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

use Bouledepate\JsonRpc\Exceptions\InvalidRequestException;
use Bouledepate\JsonRpc\Model\Dataset;

/**
 * Defines the validation mechanism for JSON-RPC requests.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
interface ValidatorInterface
{
    /**
     * Validates the provided dataset against the JSON-RPC request requirements.
     *
     * @param Dataset $dataset The dataset representing the JSON-RPC request data.
     *
     * @return void
     *
     * @throws InvalidRequestException If the dataset does not conform to the JSON-RPC specification or required criteria.
     */
    public function validate(Dataset $dataset): void;
}