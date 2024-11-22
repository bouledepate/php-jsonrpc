<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class TooManyRequestsException extends JsonRpc
{
    /**
     * @param array $content Additional data related to the exception, such as the maximum allowed batch size.
     */
    public function __construct(array $content)
    {
        parent::__construct(
            message: 'Too many batch requests',
            code: -32603,
            content: $content
        );
    }
}
