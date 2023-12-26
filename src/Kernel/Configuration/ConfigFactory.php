<?php

declare(strict_types=1);

namespace Kernel\Configuration;

use Kernel\Environment\Variables;

final readonly class ConfigFactory
{
    public function __construct(private array $env = [])
    {
    }

    public function getConfig(ConfigType $type): Config
    {
        return match ($type) {
            ConfigType::Application => $this->collectApplicationConfig(),
            ConfigType::JsonRpc => $this->collectJsonRpcConfig()
        };
    }

    private function getEnvironmentValue(Variables $name): ?string
    {
        if (array_key_exists($name->name, $this->env) === false) {
            return null;
        }
        return $this->env[$name->name];
    }

    private function collectApplicationConfig(): ApplicationConfig
    {
        return new ApplicationConfig(
            env: $this->getEnvironmentValue(Variables::ENVIRONMENT),
            errorDetails: (bool)$this->getEnvironmentValue(Variables::DISPLAY_ERROR_DETAILS),
            logErrors: (bool)$this->getEnvironmentValue(Variables::LOG_ERRORS),
            logErrorDetails: (bool)$this->getEnvironmentValue(Variables::LOG_ERROR_DETAILS),
            commandsDir: $this->getEnvironmentValue(Variables::COMMANDS_CONFIG),
            definitionsDir: $this->getEnvironmentValue(Variables::DEFINITIONS_CONFIG)
        );
    }

    private function collectJsonRpcConfig(): JsonRpcConfig
    {
        return new JsonRpcConfig(
            entrypoint: $this->getEnvironmentValue(Variables::ENTRYPOINT),
            defaultEntrypoint: (bool)$this->getEnvironmentValue(Variables::USE_DEFAULT_ENTRYPOINT),
            batchRequests: (bool)$this->getEnvironmentValue(Variables::BATCH_REQUESTS)
        );
    }
}