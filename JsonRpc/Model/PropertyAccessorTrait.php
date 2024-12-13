<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

/**
 * @package Bouledepate\JsonRpc\Model
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
trait PropertyAccessorTrait
{
    abstract public function getData(): mixed;

    public function getProperty(string $property, mixed $default = null): mixed
    {
        $content = $this->getData();

        if (empty($content)) {
            return $default;
        }

        $subKeys = array_map(
            callback: fn(string $part) => str_replace('\\.', '.', $part),
            array: preg_split('/(?<!\\\\)\./', $property)
        );

        foreach ($subKeys as $subKey) {
            if (is_array($content) && array_key_exists($subKey, $content)) {
                $content = $content[$subKey];
            } else {
                return $default;
            }
        }

        return $content;
    }

    public function getPropertyByIndex(int $index, mixed $default = null): mixed
    {
        $content = $this->getData();

        return is_array($content) && array_key_exists($index, $content) ? $content[$index] : $default;
    }

    public function hasProperty(string $property): bool
    {
        $content = $this->getData();

        if (empty($content)) {
            return false;
        }

        $subKeys = array_map(
            callback: fn(string $part) => str_replace('\\.', '.', $part),
            array: preg_split('/(?<!\\\\)\./', $property)
        );

        foreach ($subKeys as $subKey) {
            if (is_array($content) && array_key_exists($subKey, $content)) {
                $content = $content[$subKey];
            } else {
                return false;
            }
        }

        return true;
    }
}