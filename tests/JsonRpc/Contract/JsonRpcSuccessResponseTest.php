<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Contract;

use Bouledepate\JsonRpc\Contract\SuccessJsonRpcResponse;
use PHPUnit\Framework\TestCase;

final class JsonRpcSuccessResponseTest extends TestCase
{
    public function testCreateSuccessResponse(): void
    {
        $response = new SuccessJsonRpcResponse(1, null);

        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals(1, $response->getId());
        $this->assertEquals(['result' => null], $response->getContent());
    }

    public function testResponseHasValidBody(): void
    {
        $response = new SuccessJsonRpcResponse(1, ['data' => ['banana', 'apple']]);

        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals(1, $response->getId());
        $this->assertEquals(['result' => ['data' => ['banana', 'apple']]], $response->getContent());

        $this->assertArrayHasKey('result', $response->toArray());
        $this->assertArrayNotHasKey('error', $response->toArray());

        $this->assertArrayHasKey('result', $response->jsonSerialize());
        $this->assertArrayNotHasKey('error', $response->jsonSerialize());
    }

    public function testResponseCanBeSerialized(): void
    {
        $response = new SuccessJsonRpcResponse(1, true);
        $expected = json_encode(['jsonrpc' => '2.0', 'id' => 1, 'result' => true]);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($response));
    }
}