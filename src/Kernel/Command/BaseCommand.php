<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command;

use JRPC\Kernel\Command\Data\CommandDTO;
use JRPC\Kernel\Command\Interfaces\CommandInterface;

abstract class BaseCommand implements CommandInterface
{
    protected readonly CommandDTO $payload;
    protected mixed $result = null;

    final public function setPayload(CommandDTO $DTO): void
    {
        $this->payload = $DTO;
    }

    final public function getResult(): mixed
    {
        return $this->result;
    }

    public function setResult(mixed $data): void
    {
        $this->result = $data;
    }
}