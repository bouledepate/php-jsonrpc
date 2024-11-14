<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

/**
 * @package Bouledepate\JsonRpc\Contract
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final readonly class ErrorJsonRpcResponse extends JsonRpcResponse
{
    /**
     * @param string|int|null $id The identifier of the request.
     * @param array $error The error details.
     */
    public function __construct(
        string|int|null $id,
        private array $error
    ) {
        parent::__construct($id);
    }

    /**
     * Retrieves the content of the error response.
     *
     * @return array The content containing the error details.
     */
    public function getContent(): array
    {
        return [
            'error' => $this->error,
        ];
    }
}