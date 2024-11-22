<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Bouledepate\JsonRpc\Interfaces\ArrayOutputInterface;
use Bouledepate\JsonRpc\Interfaces\ContentInterface;
use Exception;
use RuntimeException;

/**
 * @package Bouledepate\JsonRpc\Exceptions
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
abstract class JsonRpc extends Exception implements ContentInterface
{
    /**
     * @var mixed Additional content for the exception.
     */
    protected mixed $content = null;

    /**
     * @param string         $message  The exception message.
     * @param int            $code     The exception code.
     * @param array          $content  Additional content for the exception.
     * @param Exception|null $previous Previous exception for exception chaining.
     */
    public function __construct(
        string $message,
        int $code,
        mixed $content = null,
        ?Exception $previous = null
    ) {
        $this->content = $content;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Sets the additional content for the exception.
     *
     * @param array $content The additional content.
     */
    final public function setContent(mixed $content): void
    {
        $this->content = $content;
    }

    /**
     * Retrieves the additional content for the exception.
     *
     * @return mixed The additional content.
     */
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
