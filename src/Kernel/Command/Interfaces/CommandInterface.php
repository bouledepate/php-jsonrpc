<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Interfaces;

use JRPC\Kernel\Command\Data\CommandDTO;

interface CommandInterface
{
    public function execute(): void;

    public function setPayload(CommandDTO $DTO): void;

    public function setResult(mixed $data): void;

    public function getResult(): mixed;
}