<?php

declare(strict_types=1);

namespace Kernel\Configuration;

enum Environment: string
{
    case Develop = 'develop';
    case Stage = 'stage';
    case Test = 'test';
    case Production = 'production';

    public static function values(): array
    {
        $cases = self::cases();
        return array_map(
            callback: fn(string $value) => strtolower($value),
            array: array_column($cases, 'name')
        );
    }

    public static function current(string $env): Environment
    {
        return self::from($env);
    }
}