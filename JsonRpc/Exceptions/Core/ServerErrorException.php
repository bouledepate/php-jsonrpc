<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class ServerErrorException extends JsonRpcException
{
    protected mixed $content = [
        'A server error occurred. This is an implementation-defined server error'
    ];

    public function __construct(
        array $content = [],
        string $message = 'Server error',
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $this->content = $rewrite ? $content : array_merge($this->content, $content);

        parent::__construct(
            message: $message,
            code: -32000,
            content: $content,
            previous: $previous
        );
    }
}
