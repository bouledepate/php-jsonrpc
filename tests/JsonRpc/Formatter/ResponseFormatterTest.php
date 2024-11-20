<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc\Tests\Formatter;

use Bouledepate\JsonRpc\Contract\JsonRpcRequest;
use Bouledepate\JsonRpc\Exceptions\Core\InvalidRequestException;
use Bouledepate\JsonRpc\Exceptions\Core\ParseErrorException;
use Bouledepate\JsonRpc\Formatter\FormatterInterface;
use Bouledepate\JsonRpc\Formatter\ResponseFormatter;
use Bouledepate\JsonRpc\Interfaces\ExceptionContentInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ResponseFormatterTest extends TestCase
{
    private readonly FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ResponseFormatter();
    }

    public function testFormatError(): void
    {
        $exception = new Exception('message', 123);
        $result = $this->formatter->formatError($exception);

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('data', $result);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(['code' => 123, 'message' => 'message'], $result, []);

    }

    public function testFormatErrorWhichImplementsContentInterface(): void
    {
        $exception = new class extends Exception implements ExceptionContentInterface {
            public function getContent(): array
            {
                return ['test', 'key'];
            }
        };

        $result = $this->formatter->formatError(new $exception('message', 123));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(['code' => 123, 'message' => 'message', 'data' => ['test', 'key']], $result, []);
    }

    public function testFormatSuccessResponse(): void
    {
        $request = new JsonRpcRequest(123, 'test');
        $response = $this->getPsrResponse(json_encode(['data' => true]), function (string $json) {
            $data = json_decode($json, true);
            $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
                expected: ['jsonrpc' => '2.0', 'result' => ['data' => true], 'id' => 123],
                actual: $data,
                keysToBeIgnored: []
            );
        });

        $this->formatter->formatResponse($request, $response);
    }

    public function testFormatSuccessEmptyResponse(): void
    {
        $request = new JsonRpcRequest(123, 'test');
        $response = $this->getPsrResponse("", function (string $json) {
            $data = json_decode($json, true);
            $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
                expected: ['jsonrpc' => '2.0', 'result' => null, 'id' => 123],
                actual: $data,
                keysToBeIgnored: []
            );
        });

        $this->formatter->formatResponse($request, $response);
    }

    public function testFormatErrorResponse(): void
    {
        $request = new JsonRpcRequest(123, 'test');
        $exception = new Exception('test', 123);
        $response = $this->getPsrResponse("", function (string $json) {
            $data = json_decode($json, true);
            $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
                expected: ['jsonrpc' => '2.0', 'error' => ['code' => 123, 'message' => 'test'], 'id' => 123],
                actual: $data,
                keysToBeIgnored: []
            );
        });

        $this->formatter->formatInvalidResponse($request, $response, $exception);
    }

    public function testFormatParseErrorResponse(): void
    {
        $request = new JsonRpcRequest(123, 'test');
        $response = $this->getPsrResponse("", function (string $json) {
            $data = json_decode($json, true);
            $this->assertEquals(null, $data['id']);
            $this->assertArrayHasKey('error', $data);
        });

        $exception = new ParseErrorException();
        $this->formatter->formatInvalidResponse($request, $response, $exception);
    }

    public function testFormatInvalidRequestResponse(): void
    {
        $request = new JsonRpcRequest(123, 'test');
        $response = $this->getPsrResponse("", function (string $json) {
            $data = json_decode($json, true);
            $this->assertEquals(null, $data['id']);
            $this->assertArrayHasKey('error', $data);
        });

        $exception = new InvalidRequestException();
        $this->formatter->formatInvalidResponse($request, $response, $exception);
    }

    private function getPsrResponse(string $jsonResponse, callable $assert): ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($jsonResponse);
        $stream->expects(self::once())->method('rewind');
        $stream->expects(self::once())->method('write')->willReturnCallback(
            function (string $json) use ($assert) {
                $assert($json);
                return 1;
            }
        );

        $psrResponse = $this->createStub(ResponseInterface::class);
        $psrResponse->method('getBody')->willReturn($stream);
        $psrResponse->method('withHeader')->willReturnSelf();
        $psrResponse->method('withStatus')->willReturnSelf();

        return $psrResponse;
    }
}