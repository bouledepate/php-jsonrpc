<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Stack;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\SuccessJsonRpcResponse;
use Bouledepate\JsonRpc\Stack\ResponseStack;
use PHPUnit\Framework\TestCase;

final class ResponseStackTest extends TestCase
{
    public function testPushElement(): void
    {
        $stack = new ResponseStack();

        $stack->push(...$this->getRequestPair(1));
        $stack->push(...$this->getRequestPair(2));

        $this->assertCount(2, $stack->all());
    }

    public function testPopElement(): void
    {
        $stack = new ResponseStack();
        $element = $stack->pop();
        $this->assertNull($element);

        $stack->push(...$this->getRequestPair(1));
        $stack->push(...$this->getRequestPair(2));

        $element = $stack->pop();
        $this->assertEquals(2, $element->getRequest()->getId());
    }

    public function testFlushStack(): void
    {
        $stack = new ResponseStack();
        $stack->push(...$this->getRequestPair(1));
        $stack->push(...$this->getRequestPair(2));

        $result = $stack->flush();

        $this->assertEmpty($stack->all());
        $this->assertNull($stack->pop());
        $this->assertCount(2, $result);
    }

    public function testIsEmpty(): void
    {
        $stack = new ResponseStack();
        $this->assertTrue($stack->isEmpty());

        $stack->push(...$this->getRequestPair(1));
        $this->assertFalse($stack->isEmpty());
    }

    public function testIsSingle(): void
    {
        $stack = new ResponseStack();
        $this->assertFalse($stack->isSingleResponse());

        $stack->push(...$this->getRequestPair(1));
        $this->assertTrue($stack->isSingleResponse());

        $stack->push(...$this->getRequestPair(2));
        $this->assertFalse($stack->isSingleResponse());
    }

    public function getRequestPair(int $id): array
    {
        return [
            new JsonRpcRequest($id, 'testMethod'),
            new SuccessJsonRpcResponse($id)
        ];
    }
}