<?php

declare(strict_types=1);

namespace WoopLeague\Application\Development\Features\Environment;

use Kernel\Command\BaseCommand;
use Kernel\Command\Command;
use Kernel\Configuration\ApplicationConfig;
use Kernel\Configuration\Environment;
use Symfony\Component\String\UnicodeString;

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
        $message = $this->handleEnvironment($environment);

        $this->setResult(new GetEnvironmentResponse(
            $message
        ));
    }

    private function handleEnvironment(Environment $environment): string
    {
        $message = new UnicodeString("handled request from {$this->payload->getDeveloper()->getUsername()}. ");

        $requestEnvironment = $this->payload->getEnvironment();
        $message = $message->append(
            $environment->equals($requestEnvironment) ? 'yes, current env is ' :  'no, current env is not ',
            $requestEnvironment,
        )->ensureEnd('. ');

        if ($this->payload->getDeveloper()->getEmail() !== null) {
            $message = $message->append(
                "we'll send results to your email: ",
                $this->payload->getDeveloper()->getEmail()
            );
        }

        return $message->trim()->toString();
    }
}