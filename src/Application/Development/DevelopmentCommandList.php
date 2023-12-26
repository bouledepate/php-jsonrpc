<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development;

use Kernel\Command\Interfaces\CommandProvider;
use WoopLeague\Application\Development\Features\Environment\GetEnvironment;

final readonly class DevelopmentCommandList implements CommandProvider
{
    public function commands(): array
    {
        return [
            GetEnvironment::class,
        ];
    }
}