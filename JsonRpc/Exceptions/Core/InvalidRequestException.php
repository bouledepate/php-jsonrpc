<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class InvalidRequestException extends JsonRpcException
{

    protected mixed $content = [
        'The JSON sent is not a valid Request object'
    ];

    public function __construct(
        array $content = [],
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $this->content = $rewrite ? $content : array_merge($this->content, $content);

        parent::__construct(
            message: 'Invalid Request',
            code: -32600,
            content: $content,
            previous: $previous
        );
    }
}
