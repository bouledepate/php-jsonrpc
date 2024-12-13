<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Interfaces\ArrayOutputInterface;
use Bouledepate\JsonRpc\Model\Method;
use Bouledepate\JsonRpc\Model\Params;
use JsonSerializable;

/**
 * @package Bouledepate\JsonRpc\Contract
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
readonly class JsonRpcRequest implements ArrayOutputInterface, JsonSerializable
{
    private const VERSION = '2.0';

    private int|string|null $id;

    private ?Method $method;

    private ?Params $params;

    private bool $isNotification;

    public function __construct(
        int|string|null $id,
        ?string $method,
        ?array $params = null,
        bool $isNotification = false
    ) {
        $this->id = $id;
        $this->method = $method !== null ? new Method($method) : null;
        $this->params = $params !== null ? new Params($params) : null;
        $this->isNotification = $isNotification;
    }

    public function getVersion(): string
    {
        return self::VERSION;
    }

    public function getMethod(): ?Method
    {
        return $this->method;
    }

    public function getParams(): ?Params
    {
        return $this->params;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function isNotification(): bool
    {
        return $this->isNotification;
    }

    public function toArray(): array
    {
        $result = [
            'jsonrpc' => self::VERSION,
            'method' => $this->method->getName(),
        ];

        if ($this->params !== null) {
            $result['params'] = $this->params->getData();
        }

        if ($this->id !== null) {
            $result['id'] = $this->id;
        }

        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}