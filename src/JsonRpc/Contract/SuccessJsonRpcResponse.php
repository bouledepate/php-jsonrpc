<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

/**
 * Represents a successful JSON-RPC response.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final readonly class SuccessJsonRpcResponse extends JsonRpcResponse
{
    /**
     * Constructor for SuccessJsonRpcResponse.
     *
     * @param string|int|null $id The identifier of the request. Use `null` for notifications.
     * @param mixed $result The result of the method execution.
     */
    public function __construct(
        string|int|null $id,
        private mixed $result = null
    ) {
        parent::__construct($id);
    }

    /**
     * Retrieves the content of the successful response.
     *
     * @return array The content containing the result.
     */
    public function getContent(): array
    {
        return [
            'result' => $this->result,
        ];
    }
}