<?php

declare(strict_types=1);

namespace Kernel\Config;

enum Environment: string
{
    case Develop = 'develop';
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