<?php

declare(strict_types=1);

namespace Kernel\Validation;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Validator;

abstract class DefaultValidator
{
    private Result $result;

    public function __construct(private readonly Validator $validator)
    {
    }

    abstract public function rules(): iterable;

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    final public function validate(ServerRequestInterface $request): void
    {
        $data = new ValidationData($request);
        $this->isValid($data) ?: $this->throwValidationException();
    }

    /** @throws ReflectionException */
    private function isValid(ValidationData $data): bool
    {
        $this->result = $this->validator->validate($data, $this->rules());
        return $this->result->isValid();
    }

    /** @throws ValidationException */
    private function throwValidationException(): never
    {
        throw new ValidationException(
            details: $this->result->getErrorMessagesIndexedByPath()
        );
    }
}