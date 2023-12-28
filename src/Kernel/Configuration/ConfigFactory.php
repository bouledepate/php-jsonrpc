<?php

declare(strict_types=1);

namespace JRPC\Kernel\Configuration;

use JRPC\Kernel\Environment\Variables;

final readonly class ConfigFactory
{
    public function __construct(private array $env)
    {
    }

    public function getConfig(ConfigType $type): Config
    {
        return match ($type) {
            ConfigType::Application => $this->collectApplicationConfig(),
            ConfigType::JsonRpc => $this->collectJsonRpcConfig()
        };
    }

    private function collectApplicationConfig(): ApplicationConfig
    {
        return new ApplicationConfig(
            env: $this->getEnvironmentValue(Variables::ENVIRONMENT),
            errorDetails: (bool)$this->getEnvironmentValue(Variables::DISPLAY_ERROR_DETAILS),
            logErrors: (bool)$this->getEnvironmentValue(Variables::LOG_ERRORS),
            logErrorDetails: (bool)$this->getEnvironmentValue(Variables::LOG_ERROR_DETAILS),
            commandsDir: $this->getEnvironmentValue(Variables::COMMANDS_CONFIG),
            definitionsDir: $this->getEnvironmentValue(Variables::DEFINITIONS_CONFIG),
            middlewaresDir: $this->getEnvironmentValue(Variables::MIDDLEWARES_CONFIG)
        );
    }

    private function collectJsonRpcConfig(): JsonRpcConfig
    {
        return new JsonRpcConfig(
            entrypoint: $this->getEnvironmentValue(Variables::JRPC_ENTRYPOINT),
            defaultEntrypoint: (bool)$this->getEnvironmentValue(Variables::JRPC_USE_DEFAULT_ENTRYPOINT),
            batchRequests: (bool)$this->getEnvironmentValue(Variables::JRPC_BATCH_REQUESTS),
            uuidRequired: (bool)$this->getEnvironmentValue(Variables::JRPC_UUID_REQUIRED)
        );
    }

    private function getEnvironmentValue(Variables $name): ?string
    {
        if (false === array_key_exists($name->name, $this->env)) {
            throw new \UnexpectedValueException("Invalid environment variable with name $name->name.");
        }

        $value = $this->env[$name->name];
        return empty($value) ? null : $value;
    }
}