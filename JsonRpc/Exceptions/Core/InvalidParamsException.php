<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class InvalidParamsException extends JsonRpcException
{
    protected mixed $content = [
        'Invalid method parameter(s)'
    ];

    public function __construct(
        array $content = [],
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $this->content = $rewrite ? $content : array_merge($this->content, $content);

        parent::__construct(
            message: 'Invalid params',
            code: -32602,
            content: $content,
            previous: $previous
        );
    }
}
