<?php

declare(strict_types=1);

namespace JRPC\Kernel\Configuration;

final readonly class ApplicationConfig implements Config
{
    public function __construct(
        private string  $env,
        private ?string $root,
        private bool    $errorDetails,
        private bool    $logErrors,
        private bool    $logErrorDetails,
        private string  $commandsDir,
        private string  $definitionsDir,
        private ?string $middlewaresDir
    )
    {
    }

    public function getEnvironment(): Environment
    {
        return Environment::current($this->env);
    }

    public function getRootPath(): string
    {
        if ($this->root !== null && ($path = realpath($this->root)) !== false) {
            return $path;
        }
        return $this->getDefaultRoot();
    }

    private function getDefaultRoot(): string
    {
        return realpath(dirname($_SERVER['DOCUMENT_ROOT']));
    }

    public function isDevelopment(): bool
    {
        $env = $this->getEnvironment();
        return Environment::Develop === $env;
    }

    public function isProduction(): bool
    {
        $env = $this->getEnvironment();
        return Environment::Production === $env;
    }

    public function isStage(): bool
    {
        $env = $this->getEnvironment();
        return Environment::Stage === $env;
    }

    public function isTest(): bool
    {
        $env = $this->getEnvironment();
        return Environment::Test === $env;
    }

    public function isDisplayErrorDetails(): bool
    {
        return $this->errorDetails;
    }

    public function isLogErrors(): bool
    {
        return $this->logErrors;
    }

    public function isLogErrorDetails(): bool
    {
        return $this->logErrorDetails;
    }

    public function getDefinitionDirectory(): string
    {
        return $this->definitionsDir;
    }

    public function getCommandsDirectory(): string
    {
        return $this->commandsDir;
    }

    public function getMiddlewaresDirectory(): ?string
    {
        return $this->middlewaresDir;
    }
}