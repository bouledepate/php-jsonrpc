<?php

declare(strict_types=1);

namespace Kernel\Middlewares;

use Kernel\Exception\JRPC\InvalidParamsException;
use Kernel\Exception\JRPC\InvalidRequestException;
use Kernel\Exception\JRPC\ParseErrorException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class RequestValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @throws InvalidRequestException
     * @throws InvalidParamsException
     * @throws ParseErrorException
     */
    public function validate(ServerRequestInterface $request): void
    {
        $json = (string)$request->getBody();
        $requestBody = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException("The request body is not a valid JSON.");
        }

        $this->validateJsonRpcVersion($requestBody['jsonrpc'] ?? null);
        $this->validateMethod($requestBody['method'] ?? null);
        $this->validateId($requestBody['id'] ?? null);
        $this->validateParams($requestBody['params'] ?? null);
    }

    /**
     * @throws InvalidRequestException
     */
    private function validateJsonRpcVersion($value): void
    {
        $violations = $this->validator->validate($value, [
            new Assert\NotNull(),
            new Assert\EqualTo('2.0')
        ]);

        if (count($violations) > 0) {
            throw new InvalidRequestException("The 'jsonrpc' version is missing or unsupported.");
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function validateMethod($value): void
    {
        $violations = $this->validator->validate($value, [
            new Assert\NotNull(),
            new Assert\Type('string'),
            new Assert\NotBlank()
        ]);

        if (count($violations) > 0) {
            throw new InvalidRequestException("The 'method' field is missing or does not correspond to any known method.");
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function validateId($value): void
    {
        $violations = $this->validator->validate($value, [
            new Assert\NotNull(),
            new Assert\Uuid()
        ]);

        if (count($violations) > 0) {
            throw new InvalidRequestException("The 'id' field is invalid. It must be a UUID version 4 string.");
        }
    }

    /**
     * @throws InvalidParamsException
     */
    private function validateParams($value): void
    {
        $violations = $this->validator->validate($value, [
            new Assert\Type('array')
        ]);

        if (count($violations) > 0) {
            throw new InvalidParamsException("The 'params' field is invalid or not properly structured. It must be an array.");
        }
    }
}
