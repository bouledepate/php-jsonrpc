<?php

declare(strict_types=1);

namespace Application\Development;

use JRPC\Kernel\Command\Interfaces\CommandProvider;
use Application\Development\Features\CheckEnvironment\CheckEnvironment;
use Application\Development\Features\GetEnvironment\GetEnvironment;

final readonly class DevelopmentCommandList implements CommandProvider
{
    public function commands(): array
    {
        return [
            GetEnvironment::class,
            CheckEnvironment::class
        ];
    }
}