<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

/**
 * @package Bouledepate\JsonRpc\Contract
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final readonly class SuccessJsonRpcResponse extends JsonRpcResponse
{
    public function __construct(
        string|int|null $id,
        private mixed $result = null
    ) {
        parent::__construct($id);
    }

    public function getContent(): array
    {
        return [
            'result' => $this->result,
        ];
    }
}