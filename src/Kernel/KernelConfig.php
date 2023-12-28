<?php

declare(strict_types=1);

namespace JRPC\Kernel;

use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Configuration\JsonRpcConfig;

final readonly class KernelConfig
{
    public function __construct(
        private ApplicationConfig $mainConfig,
        private JsonRpcConfig     $jrpcConfig
    )
    {
    }

    public function getJrpcConfig(): JsonRpcConfig
    {
        return $this->jrpcConfig;
    }

    public function getMainConfig(): ApplicationConfig
    {
        return $this->mainConfig;
    }
}