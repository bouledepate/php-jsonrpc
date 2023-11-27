<?php

declare(strict_types=1);

namespace Kernel\Validation\Rule\Array;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final readonly class ArrayValueHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof ArrayValue) {
            throw new UnexpectedRuleException(ArrayValue::class, $rule);
        }

        if ($rule->isEmptyAllowed() === false && empty($value)) {
            return (new Result)->addError($rule->getEmptyValueMessage());
        }

        if (!is_array($value) || !is_iterable($value)) {
            return (new Result)->addError($rule->getInvalidValueMessage());
        }
        return new Result;
    }
}