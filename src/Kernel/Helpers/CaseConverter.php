<?php

declare(strict_types=1);

namespace Kernel\Helpers;

final readonly class CaseConverter
{
    /**
     * Convert a string to camelCase.
     *
     * @param string $string The string to convert.
     * @return string The camelCased string.
     */
    public static function toCamelCase(string $string): string
    {
        $result = strtolower($string);
        preg_match_all('/_[a-z]/', $result, $matches);
        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }

    /**
     * Convert a string to snake_case.
     *
     * @param string $string The string to convert.
     * @return string The snake_cased string.
     */
    public static function toSnakeCase(string $string): string
    {
        $result = preg_replace('/[A-Z]/', '_$0', $string);
        return strtolower(trim($result, '_'));
    }
}