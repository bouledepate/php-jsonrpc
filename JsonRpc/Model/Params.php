<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

/**
 * @package Bouledepate\JsonRpc\Model
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
readonly class Params
{
    use PropertyAccessorTrait;

    /**
     * The content of the parameters.
     *
     * @var array The parameters as an associative array.
     */
    private array $content;

    /**
     * Constructor for Params.
     *
     * @param array $content The parameters content.
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    /**
     * Retrieves the data of the parameters.
     *
     * @return array The parameters.
     */
    final public function getData(): array
    {
        return $this->content;
    }
}
