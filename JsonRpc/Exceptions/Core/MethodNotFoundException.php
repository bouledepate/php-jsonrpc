<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class MethodNotFoundException extends JsonRpcException
{
    /**
     * @var mixed|string[] Additional content for the exception.
     */
    protected mixed $content = [
        'details' => 'The method does not exist or is not available'
    ];

    public function __construct(
        array $content = [],
        bool $rewrite = true,
        ?Exception $previous = null
    ) {
        $this->content = $rewrite ? $content : array_merge($this->content, $content);

        parent::__construct(
            message: 'Method not found',
            code: -32601,
            content: $content,
            previous: $previous
        );
    }
}
