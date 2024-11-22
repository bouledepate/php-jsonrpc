<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Bouledepate\JsonRpc\Exceptions\JsonRpc;
use Exception;

/**
 * @package Bouledepate\JsonRpc\Exceptions\Core
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
final class MethodNotFoundException extends JsonRpc
{
    /**
     * @var mixed|string[] Additional content for the exception.
     */
    protected mixed $content = [
        'details' => 'The method does not exist or is not available'
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
            message: 'Method not found',
            code: -32601,
            content: $content,
            previous: $previous
        );
    }
}
