<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Model;

use Bouledepate\JsonRpc\Model\Params;
use PHPUnit\Framework\TestCase;

final class ParamsTest extends TestCase
{
    public function testCreateParams(): void
    {
        $params = new Params(['test' => 123]);
        $this->assertSame(['test' => 123], $params->getData());
    }
}