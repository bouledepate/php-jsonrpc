<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Contract;

use Bouledepate\JsonRpc\Interfaces\ArrayOutputInterface;
use JsonSerializable;

/**
 * Abstract class representing a JSON-RPC response.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
abstract readonly class JsonRpcResponse implements ArrayOutputInterface, JsonSerializable
{
    protected const VERSION = '2.0';

    /**
     * The identifier of the request. Can be an integer, string, or null for notifications.
     *
     * @var int|string|null
     */
    protected int|string|null $id;

    /**
     * Constructor for JsonRpcResponse.
     *
     * @param int|string|null $id The identifier of the request.
     */
    public function __construct(int|string|null $id)
    {
        $this->id = $id;
    }

    /**
     * Retrieves the JSON-RPC version.
     *
     * @return string The JSON-RPC version.
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Retrieves the identifier of the request.
     *
     * @return int|string|null The request identifier.
     */
    public function getId(): int|string|null
    {
        return $this->id;
    }

    /**
     * Retrieves the content of the response.
     *
     * @return mixed The content of the response.
     */
    abstract public function getContent(): mixed;

    /**
     * Specifies data which should be serialized to JSON.
     *
     * @return array The data to be serialized.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Converts the response to an associative array.
     *
     * @return array The response as an associative array.
     */
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