<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Exception;

/**
 * Represents an error due to invalid parameters in a JSON-RPC request.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final class InvalidParamsException extends JsonRpcException
{
    /**
     * Constructor for InvalidParamsException.
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
            'details' => 'Invalid method parameter(s).'
        ];

        $content = $rewrite ? $content : array_merge($defaultContent, $content);

        parent::__construct(
            message: 'Invalid params.',
            code: -32602,
            content: $content,
            rewrite: $rewrite,
            previous: $previous
        );
    }
}
