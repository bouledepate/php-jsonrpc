<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

use Bouledepate\JsonRpc\Exceptions\MethodNotFoundException;

/**
 * Represents a JSON-RPC method.
 * Ensures the validity of the method name and provides access to its name.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
readonly class Method
{
    /**
     * Method constructor.
     *
     * Initializes the object with the method name and validates it.
     *
     * @param string $name The name of the JSON-RPC method.
     *
     * @throws MethodNotFoundException If the method name starts with "rpc.", which is reserved for internal use.
     */
    public function __construct(private string $name)
    {
        if (preg_match('/^rpc.+$/', $this->name)) {
            throw new MethodNotFoundException(
                content: ['note' => 'Method names starting with "rpc." are reserved for internal use only.'],
                rewrite: false
            );
        }
    }

    /**
     * Retrieves the name of the method.
     *
     * @return string The name of the JSON-RPC method.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
