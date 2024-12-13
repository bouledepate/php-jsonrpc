<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Contract;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Model\Method;
use Bouledepate\JsonRpc\Model\Params;
use PHPUnit\Framework\TestCase;

final class JsonRpcRequestTest extends TestCase
{
    public function testCreateEmptyRequest(): void
    {
        $request = new JsonRpcRequest(null, null, null, false);

        $this->assertEquals('2.0', $request->getVersion());
        $this->assertNull($request->getId());
        $this->assertNull($request->getMethod());
        $this->assertNull($request->getParams());
        $this->assertFalse($request->isNotification());
    }

    public function testCreateRequest(): void
    {
        $request = new JsonRpcRequest(1, 'testMethod', null);

        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals(1, $request->getId());
        $this->assertNull($request->getParams());
        $this->assertFalse($request->isNotification());

        $this->assertInstanceOf(Method::class, $request->getMethod());
        $this->assertEquals('testMethod', $request->getMethod()->getName());
    }

    public function testCreateRequestWithParams(): void
    {
        $request = new JsonRpcRequest(1, 'testMethod', ['banana', 'apple', 'orange']);

        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals(1, $request->getId());

        $this->assertInstanceOf(Method::class, $request->getMethod());
        $this->assertEquals('testMethod', $request->getMethod()->getName());

        $this->assertInstanceOf(Params::class, $request->getParams());
        $this->assertEquals(['banana', 'apple', 'orange'], $request->getParams()->getData());
    }

    public function testCreateRequestWithInvalidMethodName(): void
    {
        $this->expectException(MethodNotFoundException::class);
        new JsonRpcRequest(1, 'rpc.testMethod');
    }

    public function testGetRequestAsArray(): void
    {
        $request = new JsonRpcRequest(1, 'testMethod');
        $this->assertEquals(['jsonrpc' => '2.0', 'method' => 'testMethod', 'id' => 1], $request->toArray());
        $this->assertEquals(['jsonrpc' => '2.0', 'method' => 'testMethod', 'id' => 1], $request->jsonSerialize());

        $request = new JsonRpcRequest(1, 'testMethod', ['banana', 'apple', 'orange']);
        $this->assertEquals(['jsonrpc' => '2.0', 'method' => 'testMethod', 'params' => ['banana', 'apple', 'orange'], 'id' => 1], $request->toArray());
        $this->assertEquals(['jsonrpc' => '2.0', 'method' => 'testMethod', 'params' => ['banana', 'apple', 'orange'], 'id' => 1], $request->jsonSerialize());
    }

    public function testGetRequestAsJson(): void
    {
        $request = new JsonRpcRequest(1, 'testMethod');
        $expected = json_encode(['jsonrpc' => '2.0', 'method' => 'testMethod', 'id' => 1]);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($request));

        $request = new JsonRpcRequest(1, 'testMethod', ['banana', 'apple', 'orange']);
        $expected = json_encode(['jsonrpc' => '2.0', 'method' => 'testMethod', 'params' => ['banana', 'apple', 'orange'], 'id' => 1]);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($request));
    }
}