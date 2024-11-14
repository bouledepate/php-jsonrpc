<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
final class InvalidRequestException extends JsonRpcException
{
    /**
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
            'details' => 'The JSON sent is not a valid Request object'
        ];

        $content = $rewrite ? $content : array_merge($defaultContent, $content);

        parent::__construct(
            message: 'Invalid Request',
            code: -32600,
            content: $content,
            rewrite: $rewrite,
            previous: $previous
        );
    }
}
