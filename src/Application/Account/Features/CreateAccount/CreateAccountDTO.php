<?php

declare(strict_types=1);

namespace WoopLeague\Application\Account\Features\CreateAccount;

use Kernel\Command\Interfaces\CommandDTO;

final readonly class CreateAccountDTO implements CommandDTO
{
    public function __construct(
        private string $username,
        private array $password
    )
    {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): array
    {
        return $this->password;
    }
}