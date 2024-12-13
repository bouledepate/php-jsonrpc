<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

/**
 * @package Bouledepate\JsonRpc\Contract
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final readonly class ErrorJsonRpcResponse extends JsonRpcResponse
{
    public function __construct(
        string|int|null $id,
        private array $error
    ) {
        parent::__construct($id);
    }

    public function getContent(): array
    {
        return [
            'error' => $this->error,
        ];
    }
}