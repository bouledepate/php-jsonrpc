<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Stack;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Contract\JsonRpcResponse;
use Bouledepate\JsonRpc\Contract\SuccessJsonRpcResponse;
use Bouledepate\JsonRpc\Stack\ResponseItem;
use PHPUnit\Framework\TestCase;

final class ResponseItemTest extends TestCase
{
    public function testCreateItem(): void
    {
        $item = new ResponseItem(...$this->getRequestPair(1));
        $this->assertInstanceOf(JsonRpcRequest::class, $item->getRequest());
        $this->assertInstanceOf(JsonRpcResponse::class, $item->getResponse());
        $this->assertFalse($item->isNotificationResponse());
    }

    public function testCreateButRequestIsNotification(): void
    {
        $item = new ResponseItem(...$this->getRequestPair(1, true));
        $this->assertInstanceOf(JsonRpcRequest::class, $item->getRequest());
        $this->assertInstanceOf(JsonRpcResponse::class, $item->getResponse());
        $this->assertTrue($item->isNotificationResponse());
    }

    public function getRequestPair(int $id, bool $notification = false): array
    {
        return [
            new JsonRpcRequest($id, 'testMethod', isNotification: $notification),
            new SuccessJsonRpcResponse($id)
        ];
    }
}