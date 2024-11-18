<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Stack;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;

final class ResponseStack
{
    private array $stack = [];

    public function push(JsonRpcRequest $request, JsonRpcResponse $response): void
    {
        $this->stack[] = new ResponseItem($request, $response);
    }

    public function flush(): array
    {
        $data = $this->all();
        $this->stack = [];

        return $data;
    }

    public function pop(): ?ResponseItem
    {
        $stack = $this->stack;

        if ($this->isEmpty()) {
            return null;
        }

        return array_pop($stack);
    }

    public function all(): array
    {
        return $this->stack;
    }

    public function isEmpty(): bool
    {
        return empty($this->stack);
    }

    public function isSingleResponse(): bool
    {
        return count($this->stack) === 1;
    }
}