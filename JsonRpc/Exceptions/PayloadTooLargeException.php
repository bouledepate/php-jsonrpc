<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class PayloadTooLargeException extends JsonRpc
{
    /**
     * @param array $content Additional data related to the exception, such as the maximum allowed request size.
     */
    public function __construct(array $content)
    {
        parent::__construct(
            message: 'Request payload size exceeds limit',
            code: -32603,
            content: $content
        );
    }
}
