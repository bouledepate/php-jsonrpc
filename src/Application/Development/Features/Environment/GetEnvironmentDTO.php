<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development\Features\Environment;

use Kernel\Command\Interfaces\CommandDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class GetEnvironmentDTO implements CommandDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string        $email,
        #[Assert\Valid]
        public AdditionalDTO $subject,
    )
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): AdditionalDTO
    {
        return $this->subject;
    }
}