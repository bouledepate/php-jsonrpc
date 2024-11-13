<?php

namespace Bouledepate\JsonRpc\Interfaces;

/**
 * Defines a contract for classes that can represent their data as an array.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
interface ArrayOutputInterface
{
    /**
     * Converts the implementing object's data into an associative array.
     *
     * @return array The array representation of the object's data.
     */
    public function toArray(): array;
}
