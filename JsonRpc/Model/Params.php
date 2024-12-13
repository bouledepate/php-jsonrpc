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

    private array $content;

    public function __construct(array $content)
    {
        $this->content = $content;
    }

    final public function getData(): array
    {
        return $this->content;
    }
}
