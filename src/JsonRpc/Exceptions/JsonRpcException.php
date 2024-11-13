<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Exceptions;

use Bouledepate\JsonRpc\Interfaces\ExceptionContentInterface;
use Exception;

/**
 * Base class for all JSON-RPC exceptions.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
abstract class JsonRpcException extends Exception implements ExceptionContentInterface
{
    /**
     * Additional content for the exception.
     *
     * @var array|null
     */
    protected ?array $content;

    /**
     * Constructor for JsonRpcException.
     *
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param array $content Additional content for the exception.
     * @param Exception|null $previous Previous exception for exception chaining.
     */
    public function __construct(
        string $message,
        int $code,
        array $content = [],
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
    final public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * Retrieves the additional content for the exception.
     *
     * @return array The additional content.
     */
    final public function getContent(): array
    {
        return $this->content ?? [];
    }
}
