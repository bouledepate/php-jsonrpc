<?php

declare(strict_types=1);

namespace Kernel\Middlewares;

use Kernel\Error\JsonRpc\InvalidParamsException;
use Kernel\Error\JsonRpc\InvalidRequestException;
use Kernel\Error\JsonRpc\ParseErrorException;
use Kernel\Validation\Rule\Array\ArrayValue;
use Kernel\Validation\Rule\Uuid\Uuid;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;
use Yiisoft\Validator\EmptyCondition\WhenNull;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Validator;

final readonly class RequestValidator
{
    public function __construct(private Validator $validator)
    {
    }

    /**
     * @throws InvalidRequestException
     * @throws ParseErrorException
     * @throws InvalidParamsException
     * @throws ReflectionException
     */
    public function validate(ServerRequestInterface $request): void
    {
        $json = (string)$request->getBody();
        $requestBody = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException("The request body is not a valid JSON.");
        }

        if ($this->isValidVersion($requestBody['jsonrpc'] ?? null) === false) {
            throw new InvalidRequestException("The 'jsonrpc' version is missing or unsupported. ");
        }

        if ($this->isValidMethod($requestBody['method'] ?? null) === false) {
            throw new InvalidRequestException("The 'method' field is missing or does not correspond to any known method.");
        }

        if ($this->isValidID($requestBody['id'] ?? null) === false) {
            throw new InvalidRequestException("The 'id' field is invalid. It must be a UUID version 4 string.");
        }

        if ($this->isValidParams($requestBody['params'] ?? null) === false) {
            throw new InvalidParamsException("The 'params' field is invalid or not properly structured. It must be an array or an object.");
        }
    }

    /**
     * @throws ReflectionException
     */
    private function isValidVersion(mixed $value): bool
    {
        $rules = ['jsonrpc' => [new Required(), new Equal(targetValue: '2.0')]];
        $result = $this->validator->validate(['jsonrpc' => $value], $rules);
        return $result->isValid();
    }

    /**
     * @throws ReflectionException
     */
    private function isValidMethod(mixed $value): bool
    {
        $rules = ['method' => [new Required(), new StringValue()]];
        $result = $this->validator->validate(['method' => $value], $rules);
        return $result->isValid();
    }

    /**
     * @throws ReflectionException
     */
    private function isValidID(mixed $value): bool
    {
        $rules = ['id' => [new Composite(rules: [
            new Required(),
            new Uuid()
        ], skipOnEmpty: new WhenNull())]];
        $result = $this->validator->validate(['id' => $value], $rules);
        return $result->isValid();
    }

    /**
     * @throws ReflectionException
     */
    private function isValidParams(mixed $value): bool
    {
        $rules = ['params' => [new Composite(rules: [new ArrayValue(allowEmpty: true)], skipOnEmpty: new WhenNull())]];
        $result = $this->validator->validate(['params' => $value], $rules);
        return $result->isValid();
    }
}