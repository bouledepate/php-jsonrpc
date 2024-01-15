<?php

declare(strict_types=1);

namespace Application\Development\Features\GetEnvironment;

use JRPC\Kernel\Command\BaseCommand;
use JRPC\Kernel\Command\Command;
use JRPC\Kernel\Configuration\ApplicationConfig;

#[Command(name: 'development.getEnvironment')]
final class GetEnvironment extends BaseCommand
{
    public function __construct(private readonly ApplicationConfig $config)
    {
    }

    public function execute(): void
    {
        $environment = $this->config->getEnvironment();

        $this->writeResult(new GetEnvironmentResponse(
            environment: $environment->value
        ));
    }
}