<?php

declare(strict_types=1);

namespace Demo\BillApi;

use Bouledepate\JsonRpc\Interfaces\MethodProviderInterface;
use Bouledepate\JsonRpc\Model\Method;

final readonly class JsonRpcMethodProvider implements MethodProviderInterface
{
    /**
     * @param array<string, callable> $handlers
     */
    public function __construct(private array $handlers)
    {
    }

    public function exist(Method $method): bool
    {
        return array_key_exists($method->getName(), $this->handlers);
    }

    /**
     * @return array<string, callable>
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}
