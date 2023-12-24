<?php

declare(strict_types=1);

namespace Kernel\ValueObject;

use Stringable;

abstract class ValueObject implements Stringable
{
    public abstract function getValue(): mixed;

    public abstract function equals(ValueObject $object): bool;
}