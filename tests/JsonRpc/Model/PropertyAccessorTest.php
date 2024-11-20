<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Model;

use Bouledepate\JsonRpc\Model\Params;
use PHPUnit\Framework\TestCase;

final class PropertyAccessorTest extends TestCase
{
    public function testGetProperty(): void
    {
        $object = new Params(['test' => true]);
        $this->assertTrue($object->getProperty('test'));
    }

    public function testGetInvalidProperty(): void
    {
        $object = new Params(content: []);
        $this->assertNull($object->getProperty('test'));
    }

    public function testGetInvalidPropertyWithDefaultValue(): void
    {
        $object = new Params(content: []);
        $this->assertSame('default-value', $object->getProperty('test', 'default-value'));
    }

    public function testGetDotNotatedProperty(): void
    {
        $object = new Params(['test' => ['key' => 'value']]);
        $this->assertEquals('value', $object->getProperty('test.key'));
    }

    public function testGetPropertyWithEncodedKey(): void
    {
        $object = new Params(['test.key' => 123]);
        $this->assertEquals(123, $object->getProperty('test\\.key'));
        $this->assertNull($object->getProperty('test.key'));
    }

    public function testGetDotNotatedPropertyWithEncodedKey(): void
    {
        $object = new Params(['test.key' => ['value' => 123]]);
        $this->assertEquals(123, $object->getProperty('test\\.key.value'));
        $this->assertNull($object->getProperty('test.key.value'));
    }

    public function testGetPropertyByIndex(): void
    {
        $object = new Params(['banana', 'key', 'value']);
        $this->assertSame('banana', $object->getPropertyByIndex(0));
        $this->assertSame('value', $object->getPropertyByIndex(2));
    }

    public function testGetPropertyByInvalidIndex(): void
    {
        $object = new Params(['banana', 'key', 'value']);
        $this->assertNull($object->getPropertyByIndex(4));
    }

    public function testGetPropertyByInvalidIndexAndDefinedDefaultValue(): void
    {
        $object = new Params(['banana', 'key', 'value']);
        $this->assertSame('default-value', $object->getPropertyByIndex(4, 'default-value'));
    }

    public function testHasProperty(): void
    {
        $object = new Params(['test' => true]);
        $this->assertTrue($object->hasProperty('test'));
        $this->assertFalse($object->hasProperty('test2'));
    }

    public function testHasNestedProperty(): void
    {
        $object = new Params(['test' => ['key' => 'value']]);
        $this->assertTrue($object->hasProperty('test.key'));
    }

    public function testHasDotNotatedProperty(): void
    {
        $object = new Params(['test.key' => 'value']);
        $this->assertTrue($object->hasProperty('test\\.key'));
        $this->assertFalse($object->hasProperty('test.key'));
    }

    public function testHasDotNotatedNestedProperty(): void
    {
        $object = new Params(['test.key' => ['data' => 'value']]);
        $this->assertTrue($object->hasProperty('test\\.key.data'));
        $this->assertFalse($object->hasProperty('test.key.data'));
    }
}