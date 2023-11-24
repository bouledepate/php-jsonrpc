<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Validation\Rule\Uuid;

use Attribute;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Uuid implements
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    RuleInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;

    private mixed $skipOnEmpty = null;
    private bool $skipOnError = false;

    public function __construct(
        bool|callable|null $skipOnEmpty = null,
        bool $skipOnError = false,
        private readonly string $invalidInputMessage = 'Value must be a string.',
        private readonly string $invalidUuidMessage = 'Value must be valid UUID4 string.'
    ) {
        $this->skipOnError = $skipOnError;
        $this->skipOnEmpty = $skipOnEmpty;
    }

    public function getName(): string
    {
        return 'uuid';
    }

    public function getHandler(): string
    {
        return UuidHandler::class;
    }

    public function getInvalidInputMessage(): string
    {
        return $this->invalidInputMessage;
    }

    public function getInvalidUuidMessage(): string
    {
        return $this->invalidUuidMessage;
    }
}