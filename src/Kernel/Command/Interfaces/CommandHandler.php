<?php

declare(strict_types=1);

namespace Kernel\Command\Interfaces;

interface CommandHandler
{
    public function handle(?CommandDTO $data): mixed;
}