<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

/**
 * Defines how the content of an object should be retrieved.
 *
 * @package Bouledepate\JsonRpc\Interfaces
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
interface ContentInterface
{
    /**
     * Retrieves the content or data associated with the object.
     *
     * @return mixed The content extracted from the object, which can be of any type.
     */
    public function getContent(): mixed;
}