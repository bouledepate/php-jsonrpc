<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Model;

use Bouledepate\JsonRpc\Exceptions\Core\MethodNotFoundException;
use Bouledepate\JsonRpc\Model\Method;
use PHPUnit\Framework\TestCase;

final class MethodTest extends TestCase
{
    public function testCreateMethod(): void
    {
        $method = new Method('testMethod');
        $this->assertSame('testMethod', $method->getName());
    }

    public function testCreateMethodWithInvalidName(): void
    {
        $this->expectException(MethodNotFoundException::class);
        new Method('rpc.method');
    }
}