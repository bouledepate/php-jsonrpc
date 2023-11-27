<?php

declare(strict_types=1);

namespace WoopLeague\Application\Example;

use Kernel\Data\AbstractDTO;
use Kernel\Validation\Rule\Array\ArrayValue;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Required;

final class ExampleRequest extends AbstractDTO
{
    #[Required]
    private ?string $username;
    #[BooleanValue]
    private ?bool $password;
    #[ArrayValue(skipOnEmpty: true)]
    private ?array $object;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?bool
    {
        return $this->password;
    }

    public function getObject(): ?array
    {
        return $this->object;
    }
}