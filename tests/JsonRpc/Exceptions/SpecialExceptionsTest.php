<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Exceptions;

use Bouledepate\JsonRpc\Exceptions\Core\JsonRpcException;
use Bouledepate\JsonRpc\Exceptions\PayloadTooLargeException;
use Bouledepate\JsonRpc\Exceptions\TooManyRequestsException;
use PHPUnit\Framework\TestCase;

final class SpecialExceptionsTest extends TestCase
{
    public function testPayloadTooLargeException(): void
    {
        $exception = new PayloadTooLargeException(['data' => true]);
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32603, $exception->getCode());
        $this->assertEquals('Request payload size exceeds limit', $exception->getMessage());
    }

    public function testTooManyRequestsException(): void
    {
        $exception = new TooManyRequestsException(['data' => true]);
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32603, $exception->getCode());
        $this->assertEquals('Too many batch requests', $exception->getMessage());
    }
}
