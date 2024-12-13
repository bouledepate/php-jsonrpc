<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class InternalErrorException extends JsonRpcException
{
    protected mixed $content = [
        'An internal JSON-RPC error occurred'
    ];

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
