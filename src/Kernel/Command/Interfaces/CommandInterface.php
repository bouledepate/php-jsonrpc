<?php

namespace JRPC\Kernel\Command\Interfaces;

interface CommandInterface
{
    public function execute(): void;

    public function getResult(): mixed;
}