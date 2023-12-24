<?php

declare(strict_types=1);

namespace WoopLeague\Application\Account\Features\CreateAccount;

use Random\Randomizer;

final readonly class CreateAccountResponse
{
    private string $token;

    public function __construct(private string $username)
    {
        $this->token = $this->generateToken();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    private function generateToken(): string
    {
        $generator = new Randomizer();
        return bin2hex($generator->getBytes(32));
    }
}