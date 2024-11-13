<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

use Bouledepate\JsonRpc\Exceptions\MethodNotFoundException;
use Bouledepate\JsonRpc\Interfaces\ArrayOutputInterface;
use Bouledepate\JsonRpc\Model\Method;
use Bouledepate\JsonRpc\Model\Params;
use JsonSerializable;

/**
 * Represents a JSON-RPC request.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
readonly class JsonRpcRequest implements ArrayOutputInterface, JsonSerializable
{
    private const VERSION = '2.0';

    private Method $method;
    private ?Params $params;

    /**
     * @param int|string|false $id The request ID. Use `false` for notifications.
     * @param string $method The method name.
     * @param array|null $params The method parameters.
     *
     * @throws MethodNotFoundException If the method does not exist.
     */
    public function __construct(
        private int|string|false $id,
        string $method,
        ?array $params = null
    ) {
        $this->method = new Method($method);
        $this->params = $params !== null ? new Params($params) : null;
    }

    /**
     * Gets the JSON-RPC version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Gets the method.
     *
     * @return Method
     */
    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * Gets the parameters.
     *
     * @return Params|null
     */
    public function getParams(): ?Params
    {
        return $this->params;
    }

    /**
     * Gets the request ID.
     *
     * @return int|string|false
     */
    public function getId(): int|string|false
    {
        return $this->id;
    }

    /**
     * Determines if the request is a notification (no response expected).
     *
     * @return bool
     */
    public function isNotification(): bool
    {
        return $this->id === false;
    }

    /**
     * Converts the request to an associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'jsonrpc' => self::VERSION,
            'method' => $this->method->getName(),
        ];

        if ($this->params !== null) {
            $result['params'] = $this->params->getContent();
        }

        if ($this->id !== false) {
            $result['id'] = $this->id;
        }

        return $result;
    }

    /**
     * Specifies data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}