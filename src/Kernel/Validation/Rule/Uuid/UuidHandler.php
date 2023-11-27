<?php

declare(strict_types=1);

namespace Kernel\Validation\Rule\Uuid;

use Ramsey\Uuid\Uuid as UuidValidator;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final readonly class UuidHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Uuid) {
            throw new UnexpectedRuleException(Uuid::class, $rule);
        }
        if (!is_string($value)) {
            return (new Result())->addError($rule->getInvalidInputMessage());
        }
        if (!UuidValidator::isValid($value)) {
            return (new Result())->addError($rule->getInvalidUuidMessage());
        }
        return new Result();
    }
}