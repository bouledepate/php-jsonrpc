<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Data;

use Stringable;

abstract class AbstractDTO implements Stringable
{
    public function __construct(private readonly array $params = [])
    {
    }

    public function getParameters(): array
    {
        return $this->params;
    }

    public function __toString(): string
    {
        return json_encode($this->params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}