<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Interfaces;

/**
 * Defines how the content of an exception should be retrieved.
 *
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
interface ExceptionContentInterface
{
    /**
     * Retrieves the content or data associated with the exception.
     *
     * @return mixed The content extracted from the exception, which can be of any type.
     */
    public function getContent(): mixed;
}