<?php

declare(strict_types=1);

namespace JRPC\Kernel\Configuration;

final readonly class JsonRpcConfig implements Config
{
    public function __construct(
        private string $entrypoint,
        private bool   $defaultEntrypoint,
        private bool   $batchRequests,
        private bool   $uuidRequired
    )
    {
    }

    public function getEntrypoint(): string
    {
        return $this->entrypoint;
    }

    public function useDefaultEntrypoint(): bool
    {
        return $this->defaultEntrypoint;
    }

    public function isEnabledBatchRequests(): bool
    {
        return $this->batchRequests;
    }

    public function isUuidRequired(): bool
    {
        return $this->uuidRequired;
    }
}