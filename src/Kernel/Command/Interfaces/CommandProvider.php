<?php

namespace JRPC\Kernel\Command\Interfaces;

interface CommandProvider
{
    public function commands(): array;
}