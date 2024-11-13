<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Exception;

/**
 * Represents an internal JSON-RPC error.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final class InternalErrorException extends JsonRpcException
{
    /**
     * Constructor for InternalErrorException.
     *
     * @param array $content Additional content for the exception.
     * @param bool $rewrite Whether to overwrite existing content or merge.
     * @param Exception|null $previous Previous exception for exception chaining.
     */
    public function __construct(
        array $content = [],
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $defaultContent = [
            'details' => 'An internal JSON-RPC error occurred.'
        ];

        $content = $rewrite ? $content : array_merge($defaultContent, $content);

        parent::__construct(
            message: 'Internal error.',
            code: -32603,
            content: $content,
            rewrite: $rewrite,
            previous: $previous
        );
    }
}
