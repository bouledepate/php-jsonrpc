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
    /**
     * The JSON-RPC version.
     */
    private const VERSION = '2.0';

    /**
     * The identifier of the request.
     *
     * Can be an integer, string, or null.
     *
     * @var int|string|null The request identifier.
     */
    private int|string|null $id;

    /**
     * The method to be invoked.
     *
     * @var Method|null The JSON-RPC method object.
     */
    private ?Method $method;

    /**
     * The parameters for the method.
     *
     * @var Params|null The JSON-RPC parameters object.
     */
    private ?Params $params;

    /**
     * Indicates whether the request is a notification.
     *
     * @var bool True if the request is a notification; otherwise, false.
     */
    private bool $isNotification;

    /**
     * Initializes the JSON-RPC request with the provided identifier, method name,
     * and parameters. Throws an exception if the method name is invalid.
     *
     * @param int|string|null $id The identifier of the request.
     * @param string|null $method The name of the method to invoke.
     * @param array|null $params The parameters for the method.
     * @param bool $isNotification Whether the request is a notification.
     *
     * @throws MethodNotFoundException If the method name is reserved or invalid.
     */
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

    /**
     * Gets the JSON-RPC version.
     *
     * @return string The JSON-RPC version.
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Gets the method.
     *
     * @return Method|null The JSON-RPC method object.
     */
    public function getMethod(): ?Method
    {
        return $this->method;
    }

    /**
     * Gets the parameters.
     *
     * @return Params|null The JSON-RPC parameters object.
     */
    public function getParams(): ?Params
    {
        return $this->params;
    }

    /**
     * Gets the request ID.
     *
     * @return int|string|null The identifier of the request, or null if it's a notification.
     */
    public function getId(): int|string|null
    {
        return $this->id;
    }

    /**
     * Determines if the request is a notification (no response expected).
     *
     * @return bool True if the request is a notification; otherwise, false.
     */
    public function isNotification(): bool
    {
        return $this->isNotification;
    }

    /**
     * Converts the request to an associative array.
     *
     * This method is used to serialize the request into a format suitable
     * for JSON encoding.
     *
     * @return array The associative array representation of the JSON-RPC request.
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

        if ($this->id !== null) {
            $result['id'] = $this->id;
        }

        return $result;
    }

    /**
     * Specifies data which should be serialized to JSON.
     *
     * This method is used by functions like json_encode to convert the object
     * into a JSON-compatible format.
     *
     * @return array The data that should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}