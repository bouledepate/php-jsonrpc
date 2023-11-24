<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Validation\Rule\Array;

use Attribute;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ArrayValue implements
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    RuleInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;

    private mixed $skipOnEmpty;
    private bool $skipOnError;

    public function __construct(
        bool|callable|null     $skipOnEmpty = null,
        bool                   $skipOnError = false,
        public readonly bool   $allowEmpty = false,
        public readonly string $invalidValueMessage = 'Value must be an array.',
        public readonly string $emptyValueMessage = 'Value could not be empty.'
    )
    {
        $this->skipOnError = $skipOnError;
        $this->skipOnEmpty = $skipOnEmpty;
    }

    public function getName(): string
    {
        return 'arrayValue';
    }

    public function getHandler(): string
    {
        return ArrayValueHandler::class;
    }

    public function getInvalidValueMessage(): string
    {
        return $this->invalidValueMessage;
    }

    public function getEmptyValueMessage(): string
    {
        return $this->emptyValueMessage;
    }

    public function isEmptyAllowed(): bool
    {
        return $this->allowEmpty;
    }
}