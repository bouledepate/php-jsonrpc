<?php

declare(strict_types=1);

namespace Application\Development\Features\Environment;

use JRPC\Kernel\Command\Data\CommandDTO;
use JRPC\Kernel\Configuration\Environment;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetEnvironmentDTO implements CommandDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Choice(callback: [Environment::class, 'values'])]
        private ?string       $environment,

        #[Assert\NotBlank]
        #[Assert\Valid]
        private ?DeveloperDTO $developer)
    {
    }

    public function getDeveloper(): DeveloperDTO
    {
        return $this->developer;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}