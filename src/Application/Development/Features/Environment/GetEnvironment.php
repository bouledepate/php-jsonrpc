<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development\Features\Environment;

use Kernel\Command\BaseCommand;
use Kernel\Command\Command;
use Kernel\Configuration\ApplicationConfig;
use Kernel\Configuration\Environment;

/**
 * @property GetEnvironmentDTO $payload
 * @property GetEnvironmentResponse $result
 */
#[Command(name: 'development.getEnvironment', dto: GetEnvironmentDTO::class)]
final class GetEnvironment extends BaseCommand
{
    public function __construct(private readonly ApplicationConfig $config)
    {
    }

    public function execute(): void
    {
        $environment = $this->config->getEnvironment();
        $message = $this->getMessageBy($environment);

        $this->setResult(new GetEnvironmentResponse(
            $message
        ));
    }

    private function getMessageBy(Environment $environment): string
    {
        if ($this->payload->isDeveloper()) {
            $message = "Current environment is $environment->value";
        } else {
            $message = "Sorry, you are not a developer.";
        }
        return $message;
    }
}