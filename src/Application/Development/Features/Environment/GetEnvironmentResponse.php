<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development\Features\Environment;

use Random\Randomizer;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class GetEnvironmentResponse
{
    #[SerializedName('secret_key')]
    private string $token;

    public function __construct(private string $message)
    {
        $this->token = $this->generateToken();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    private function generateToken(): string
    {
        $generator = new Randomizer();
        return bin2hex($generator->getBytes(16));
    }
}