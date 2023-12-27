<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development\Features\Environment;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeveloperDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 48)]
        private string  $username,
        #[Assert\Email]
        private ?string $email
    )
    {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}