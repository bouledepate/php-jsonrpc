<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development\Features\Environment;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class AdditionalDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 36)]
        private array $username,
        #[Assert\Type('boolean')]
        #[SerializedName('is_developer')]
        private bool   $isDeveloper,
    )
    {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isDeveloper(): bool
    {
        return $this->isDeveloper;
    }
}