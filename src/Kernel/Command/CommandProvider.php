<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Command;

interface CommandProvider
{
    public function commands(): array;
}