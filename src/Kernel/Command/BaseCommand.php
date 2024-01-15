<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command;

use JRPC\Kernel\Command\Contract\CommandRequest;
use JRPC\Kernel\Command\Data\CommandDTO;
use JRPC\Kernel\Command\Interfaces\CommandInterface;

abstract class BaseCommand implements CommandInterface
{
    private mixed $result;
    protected readonly CommandDTO $payload;
    protected readonly CommandRequest $context;

    final public function setPayload(CommandDTO $DTO): void
    {
        $this->payload = $DTO;
    }

    final public function setContext(CommandRequest $commandRequest): void
    {
        $this->context = $commandRequest;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    protected function writeResult(mixed $result): void
    {
        $this->result = $result;
    }
}