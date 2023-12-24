<?php

declare(strict_types=1);

namespace Kernel\ValueObject;

abstract class IntegerValueObject extends ValueObject
{
    public function __construct(private int $value)
    {
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(ValueObject $object): bool
    {
        return $object instanceof IntegerValueObject && $this->value === $object->getValue();
    }
}