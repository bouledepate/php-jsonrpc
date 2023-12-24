<?php

declare(strict_types=1);

namespace WoopLeague\Application\Account\Features\CreateAccount;

use Kernel\Command\Command;
use Kernel\Command\Interfaces\CommandDTO;
use Kernel\Command\Interfaces\CommandHandler;

#[Command(name: 'account.createAccount', dto: CreateAccountDTO::class)]
final class CreateAccount implements CommandHandler
{
    /**
     * @param CreateAccountDTO|null $data
     * @return CreateAccountResponse
     */
    #[\Override] public function handle(?CommandDTO $data): CreateAccountResponse
    {
        $username = $data->getUsername();
        return new CreateAccountResponse($username);
    }
}