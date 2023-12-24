<?php

declare(strict_types=1);

namespace WoopLeague\Application\Account;

use Kernel\Command\Interfaces\CommandProvider;
use WoopLeague\Application\Account\Features\CreateAccount\CreateAccount;

final readonly class AccountCommandList implements CommandProvider
{
    public function commands(): array
    {
        return [
            CreateAccount::class,
        ];
    }
}