<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class InternalErrorException extends JsonRpcException
{
    /**
     * @var mixed|string[] Additional content for the exception.
     */
    protected mixed $content = [
        'An internal JSON-RPC error occurred'
    ];

    /**
     * @param array          $content  Additional content for the exception.
     * @param bool           $rewrite  Whether to overwrite existing content or merge.
     * @param Exception|null $previous Previous exception for exception chaining.
     */
    public function __construct(
        array $content = [],
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $this->content = $rewrite ? $content : array_merge($this->content, $content);

        parent::__construct(
            message: 'Internal error',
            code: -32603,
            content: $content,
            previous: $previous
        );
    }
}
