<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Config;

use Bouledepate\JsonRpc\Config\JsonRpcOptions;
use PHPUnit\Framework\TestCase;

final class DefaultJsonRpcConfigTest extends TestCase
{
    public function testBatchSize(): void
    {
        $config = new JsonRpcOptions();
        $this->assertEquals(20, $config->getBatchSize());
    }

    public function testBatchPayloadSize(): void
    {
        $config = new JsonRpcOptions();
        $this->assertEquals(1024 * 1024, $config->getBatchPayloadSize());
    }
}