<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Config;

use Bouledepate\JsonRpc\Config\DefaultJsonRpcOptions;
use PHPUnit\Framework\TestCase;

final class DefaultJsonRpcConfigTest extends TestCase
{
    public function testBatchSize(): void
    {
        $config = new DefaultJsonRpcOptions();
        $this->assertEquals(20, $config->getBatchSize());
    }

    public function testBatchPayloadSize(): void
    {
        $config = new DefaultJsonRpcOptions();
        $this->assertEquals(1024 * 1024, $config->getBatchPayloadSize());
    }
}