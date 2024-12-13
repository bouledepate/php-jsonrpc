<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

use Bouledepate\JsonRpc\Interfaces\ArrayOutputInterface;
use JsonSerializable;

/**
 * @package Bouledepate\JsonRpc\Contract
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
abstract readonly class JsonRpcResponse implements ArrayOutputInterface, JsonSerializable
{
    protected const VERSION = '2.0';

    protected int|string|null $id;

    public function __construct(int|string|null $id)
    {
        $this->id = $id;
    }

    public function getVersion(): string
    {
        return self::VERSION;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    abstract public function getContent(): mixed;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $response = [
            'jsonrpc' => self::VERSION,
            'id' => $this->id,
        ];

        $content = $this->getContent();

        if ($content !== null) {
            $response = array_merge($response, $content);
        }

        return $response;
    }
}