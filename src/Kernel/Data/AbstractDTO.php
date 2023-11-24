<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Data;

abstract class AbstractDTO
{
    public function __construct(private readonly array $params = [])
    {
    }

    public function getParameters(): array
    {
        return $this->params;
    }
}