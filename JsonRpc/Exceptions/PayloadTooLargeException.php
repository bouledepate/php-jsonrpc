<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Bouledepate\JsonRpc\Exceptions\Core\JsonRpcException;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class PayloadTooLargeException extends JsonRpcException
{
    public function __construct(array $content)
    {
        parent::__construct(
            message: 'Request payload size exceeds limit',
            code: -32603,
            content: $content
        );
    }
}
