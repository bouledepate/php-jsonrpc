<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Model;

use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Model\Dataset;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

final class DatasetTest extends TestCase
{
    public function testCreateDataset(): void
    {
        $data = ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'test.method', 'params' => [1, 2, 3]];
        $serverRequest = $this->mockRequest(json_encode($data));

        $dataset = new Dataset($serverRequest);
        $this->assertEquals($data, $dataset->getData());
        $this->assertFalse($dataset->isBatchRequest());
    }

    public function testCreateDatasetWithInvalidJson()
    {
        $data = "{\"jsonrpc\": \"2.0\", \"id\": 123, \"method}";
        $serverRequest = $this->mockRequest($data);

        $this->expectException(ParseErrorException::class);
        new Dataset($serverRequest);
    }

    public function testCreateDatasetWithBatchRequestBody(): void
    {
        $data = [
            ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'test.method', 'params' => [1, 2, 3]],
            ['jsonrpc' => '2.0', 'id' => 2, 'method' => 'test.method', 'params' => [1, 2, 3]]
        ];
        $serverRequest = $this->mockRequest(json_encode($data));

        $dataset = new Dataset($serverRequest);
        $this->assertEquals($data, $dataset->getData());
        $this->assertTrue($dataset->isBatchRequest());
    }

    private function mockRequest(string $requestBody): ServerRequestInterface&Stub
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn($requestBody);

        $serverRequest = $this->createStub(ServerRequestInterface::class);
        $serverRequest->method('getBody')->willReturn($stream);

        return $serverRequest;
    }
}