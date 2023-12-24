<?php

namespace Kernel\Command\Interfaces;

interface CommandProvider
{
    public function commands(): array;
}