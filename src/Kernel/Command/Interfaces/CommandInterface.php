<?php

declare(strict_types=1);

namespace Kernel\Command\Interfaces;

interface CommandInterface
{
    public function execute(): void;

    public function setPayload(CommandDTO $DTO): void;

    public function setResult(mixed $data): void;

    public function getResult(): mixed;
}