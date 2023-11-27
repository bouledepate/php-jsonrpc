<?php

declare(strict_types=1);

namespace Kernel\Data;

enum BuiltinType: string
{
    case StringType = 'string';
    case IntegerType = 'int';
    case FloatType = 'float';
    case ArrayType = 'array';
    case BooleanType = 'bool';
    case MixedType = 'mixed';
}