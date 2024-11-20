<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Contract;

use Bouledepate\JsonRpc\Contract\ErrorJsonRpcResponse;
use PHPUnit\Framework\TestCase;

final class JsonRpcErrorResponseTest extends TestCase
{
    public function testCreateSuccessResponse(): void
    {
        $response = new ErrorJsonRpcResponse(1, [null]);

        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals(1, $response->getId());
        $this->assertEquals(['error' => [null]], $response->getContent());
    }

    public function testResponseHasValidBody(): void
    {
        $response = new ErrorJsonRpcResponse(1, ['data' => ['banana', 'apple']]);

        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals(1, $response->getId());
        $this->assertEquals(['error' => ['data' => ['banana', 'apple']]], $response->getContent());

        $this->assertArrayHasKey('error', $response->toArray());
        $this->assertArrayNotHasKey('result', $response->toArray());

        $this->assertArrayHasKey('error', $response->jsonSerialize());
        $this->assertArrayNotHasKey('result', $response->jsonSerialize());
    }

    public function testResponseCanBeSerialized(): void
    {
        $response = new ErrorJsonRpcResponse(1, [true]);
        $expected = json_encode(['jsonrpc' => '2.0', 'id' => 1, 'error' => [true]]);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($response));
    }
}