<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Exceptions;

use Bouledepate\JsonRpc\Exceptions\Core\InternalErrorException;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidParamsException;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Exceptions\Core\ServerErrorException;
use Bouledepate\JsonRpc\Exceptions\JsonRpcException;
use PHPUnit\Framework\TestCase;

final class CoreExceptionsTest extends TestCase
{
    public function testInternalError(): void
    {
        $exception = new InternalErrorException();
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32603, $exception->getCode());
        $this->assertEquals('Internal error', $exception->getMessage());
    }

    public function testInvalidParams(): void
    {
        $exception = new InvalidParamsException();
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32602, $exception->getCode());
        $this->assertEquals('Invalid params', $exception->getMessage());
    }

    public function testInvalidRequest(): void
    {
        $exception = new InvalidRequestException();
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32600, $exception->getCode());
        $this->assertEquals('Invalid Request', $exception->getMessage());
    }

    public function testMethodNotFound(): void
    {
        $exception = new MethodNotFoundException();
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32601, $exception->getCode());
        $this->assertEquals('Method not found', $exception->getMessage());
    }

    public function testParseError(): void
    {
        $exception = new ParseErrorException();
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32700, $exception->getCode());
        $this->assertEquals('Parse error', $exception->getMessage());
    }

    public function testServerError(): void
    {
        $exception = new ServerErrorException();
        $this->assertInstanceOf(JsonRpcException::class, $exception);
        $this->assertEquals(-32000, $exception->getCode());
        $this->assertEquals('Server error', $exception->getMessage());
    }
}