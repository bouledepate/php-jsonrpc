<?php

declare(strict_types=1);

namespace Kernel\ValueObject;

use Symfony\Component\String\UnicodeString;

abstract class StringValueObject extends ValueObject
{
    private UnicodeString $value;

    public function __construct(string $value)
    {
        $this->value = new UnicodeString($value);
    }

    public function getValue(): UnicodeString
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }

    public function equals(ValueObject $object): bool
    {
        return $this->value->equalsTo($object->getValue());
    }
}