<?php

declare(strict_types=1);

namespace Kernel\Config;

final readonly class ApplicationConfig
{
    private Environment $environment;
    private bool $displayErrorDetails;
    private bool $logErrors;
    private bool $logErrorDetails;
    private string $entrypoint;

    public function __construct(array $config)
    {
        $this->environment = Environment::current($config['ENVIRONMENT']);
        $this->displayErrorDetails = (bool)$config['DISPLAY_ERROR_DETAILS'];
        $this->logErrors = (bool)$config['LOG_ERRORS'];
        $this->logErrorDetails = (bool)$config['LOG_ERROR_DETAILS'];
        $this->entrypoint = $config['ENTRYPOINT'];
    }

    public function isDevelopment(): bool
    {
        return $this->environment === Environment::Develop;
    }

    public function isProduction(): bool
    {
        return $this->environment === Environment::Production;
    }

    public function isDisplayErrorDetails(): bool
    {
        return $this->displayErrorDetails;
    }

    public function isLogErrors(): bool
    {
        return $this->logErrors;
    }

    public function isLogErrorDetails(): bool
    {
        return $this->logErrorDetails;
    }

    public function getEntrypoint(): string
    {
        return $this->entrypoint;
    }
}