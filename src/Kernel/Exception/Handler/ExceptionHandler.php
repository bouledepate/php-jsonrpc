<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception\Handler;

use Exception;
use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\HttpStatus;
use JRPC\Kernel\Exception\JRPC\InvalidRequestException;
use JRPC\Kernel\Exception\JRPC\MethodNotFound;
use JRPC\Kernel\Exception\JRPC\ParseErrorException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

/**
 * @property Exception|AbstractException $exception
 */
final class ExceptionHandler extends SlimErrorHandler
{
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = HttpStatus::HTTP_BAD_REQUEST->value;
        $responseData = $this->getDefaultResponse();

        if ($this->isNotification()) {
            return $this->responseFactory->createResponse(HttpStatus::HTTP_NO_CONTENT->value)
                ->withHeader('Content-Type', 'application/json');
        }

        $responseData['id'] = $this->getRequestID();
        if ($exception instanceof ParseErrorException || $exception instanceof InvalidRequestException) {
            $responseData['id'] = null;
        }

        if ($exception instanceof MethodNotFound) {
            $statusCode = HttpStatus::HTTP_NOT_FOUND->value;
        }

        if ($exception instanceof HttpException) {
            $statusCode = $this->determineErrorCode();
        }

        if ($responseData['error']['data'] === null) {
            unset($responseData['error']['data']);
        }

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function isNotification(): bool
    {
        $fromAttribute = $this->request->getAttribute('is_notification');

        if ($fromAttribute === null) {
            $rawBody = (string)$this->request->getBody();
            $decodedBody = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }

            $fromAttribute = !isset($decodedBody['id']);
        }

        return (bool)$fromAttribute;
    }

    private function getRequestID(): ?string
    {
        $fromAttribute = $this->request->getAttribute('request_id');

        if ($fromAttribute === null) {
            $rawBody = (string)$this->request->getBody();
            $decodedBody = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            $fromAttribute = $decodedBody['id'] ?? null;
        }

        if (is_string($fromAttribute) === false) {
            $fromAttribute = null;
        }

        return $fromAttribute;
    }

    private function determineErrorCode(): int
    {
        return $this->exception->getCode();
    }

    private function determineErrorMessage(): string
    {
        return $this->exception->getMessage();
    }

    private function determineErrorData(): array|string|null
    {
        $data = null;
        if (method_exists($this->exception, 'getDetail')) {
            $data = $this->exception->getDetail();
        }
        return $data;
    }

    private function getDefaultResponse(): array
    {
        return [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => $this->determineErrorCode(),
                'message' => $this->determineErrorMessage(),
                'data' => $this->determineErrorData()
            ],
            'id' => null
        ];
    }
}