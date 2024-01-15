<?php

declare(strict_types=1);

namespace Application\Development\Features\CheckEnvironment;

use Application\Development\Components\TokenMiddleware;
use JRPC\Kernel\Command\BaseCommand;
use JRPC\Kernel\Command\Command;
use JRPC\Kernel\Configuration\ApplicationConfig;

/**
 * @property CheckEnvironmentDTO $payload
 */
#[Command(name: 'development.checkEnvironment', dto: CheckEnvironmentDTO::class)]
final class CheckEnvironment extends BaseCommand
{
    public function __construct(private readonly ApplicationConfig $config)
    {
    }

    public function execute(): void
    {
        $environment = $this->config->getEnvironment();
        $fromRequest = $this->payload->getEnvironment();

        $token = $this->fetchTokenFromRequest();
        $result = $environment->equals($fromRequest);

        $this->writeResult(new CheckEnvironmentResponse(
            token: $token,
            isValid: $result
        ));
    }

    public function fetchTokenFromRequest(): string
    {
        $request = $this->context->getServerRequest();
        return $request->getAttribute(TokenMiddleware::TOKEN_ATTRIBUTE);
    }
}