<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions\Core;

use Bouledepate\JsonRpc\Interfaces\ArrayOutputInterface;
use Bouledepate\JsonRpc\Interfaces\ContentInterface;
use Exception;
use RuntimeException;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
abstract class JsonRpcException extends Exception implements ContentInterface
{
    protected mixed $content = null;

    public function __construct(
        string $message,
        int $code,
        mixed $content = null,
        ?Exception $previous = null
    ) {
        $this->content = $content;
        parent::__construct($message, $code, $previous);
    }

    final public function setContent(mixed $content): void
    {
        $this->content = $content;
    }

    final public function getContent(): mixed
    {
        if ($this->content instanceof ArrayOutputInterface) {
            return $this->content->toArray();
        }

        if (is_object($this->content)) {
            throw new RuntimeException('Object must implement the ArrayOutputInterface.');
        }

        return $this->content;
    }
}
