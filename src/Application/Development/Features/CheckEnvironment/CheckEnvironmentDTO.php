<?php

declare(strict_types=1);

namespace Application\Development\Features\CheckEnvironment;

use JRPC\Kernel\Command\Data\CommandDTO;
use JRPC\Kernel\Configuration\Environment;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CheckEnvironmentDTO implements CommandDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice(callback: [Environment::class, 'values'])]
    private string $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}