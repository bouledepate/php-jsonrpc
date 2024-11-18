<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

/**
 * @package Bouledepate\JsonRpc\Model
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
trait PropertyAccessorTrait
{
    /**
     * Retrieves the content from the implementing class.
     *
     * @return mixed The content to be accessed.
     */
    abstract public function getContent(): mixed;

    /**
     * Retrieves a nested property value by a dot-notated key.
     *
     * @param string $property The dot-notated property key (e.g., "user.name").
     * @param mixed  $default  The default value to return if the property is not found.
     *
     * @return mixed The value of the property or the default value.
     */
    public function getProperty(string $property, mixed $default = null): mixed
    {
        $content = $this->getContent();

        if (empty($content)) {
            return $default;
        }

        $subKeys = explode('.', $property);

        foreach ($subKeys as $subKey) {
            if (is_array($content) && array_key_exists($subKey, $content)) {
                $content = $content[$subKey];
            } else {
                return $default;
            }
        }

        return $content;
    }

    /**
     * Retrieves a property value by its numeric index.
     *
     * @param int   $index   The index of the property.
     * @param mixed $default The default value to return if the index is not found.
     *
     * @return mixed The value at the specified index or the default value.
     */
    public function getPropertyByIndex(int $index, mixed $default = null): mixed
    {
        $content = $this->getContent();

        return is_array($content) && array_key_exists($index, $content) ? $content[$index] : $default;
    }

    /**
     * Checks if a nested property exists by a dot-notated key.
     *
     * @param string $property The dot-notated property key (e.g., "user.name").
     *
     * @return bool True if the property exists, false otherwise.
     */
    public function hasProperty(string $property): bool
    {
        $content = $this->getContent();

        if (empty($content)) {
            return false;
        }

        $subKeys = explode('.', $property);

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