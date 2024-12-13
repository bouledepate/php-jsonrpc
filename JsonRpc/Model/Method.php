<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Model;

use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;

/**
 * @package Bouledepate\JsonRpc\Model
 * @author  Semyon Shmik <promtheus815@gmail.com>
 */
readonly class Method
{
    public function __construct(private string $name)
    {
        if (preg_match('/^rpc\./', $this->name)) {
            throw new MethodNotFoundException(
                content: ['note' => 'Method names starting with "rpc." are reserved for internal use only.'],
                rewrite: false
            );
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
