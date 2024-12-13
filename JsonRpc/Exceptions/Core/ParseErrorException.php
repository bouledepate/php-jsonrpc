<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class ParseErrorException extends JsonRpcException
{
    protected mixed $content = [
        'Unable to parse request: Content-Type must be `application/json` and body must contain valid JSON'
    ];

    public function __construct(
        array $content = [],
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $this->content = $rewrite ? $content : array_merge($this->content, $content);

        parent::__construct(
            message: 'Parse error',
            code: -32700,
            content: $content,
            previous: $previous
        );
    }
}
