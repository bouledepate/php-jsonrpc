<?php

declare(strict_types=1);

namespace Kernel\Definitions;

use Kernel\Configuration\ApplicationConfig;
use Kernel\Configuration\JsonRpcConfig;
use Kernel\KernelDefinitions;
use ReflectionClass;
use ReflectionException;

final class DependencyCollector
{
    private array $providers = [];

    /**
     * @param array|DependencyProvider[] $providers
     * @throws DefinitionsFileNotFoundException
     * @throws InvalidDefinitionsFormatException
     * @throws ReflectionException
     */
    public function __construct(private readonly ApplicationConfig $config, JsonRpcConfig $jrpcConfig)
    {
        // Kernel definitions must be included.
        $this->uploadProvider(new KernelDefinitions($jrpcConfig));
        $this->loadProvidersFromConfiguration();
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @throws ReflectionException
     */
    public function collect(): array
    {
        $definitions = [];
        foreach ($this->getProviders() as $provider) {
            $definitions = array_merge($definitions, $provider->register());
        }
        return $definitions;
    }

    public function uploadProvider(DependencyProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @throws ReflectionException
     * @throws DefinitionsFileNotFoundException
     * @throws InvalidDefinitionsFormatException
     */
    private function loadProvidersFromConfiguration(): void
    {
        $definitionsPath = dirname(__DIR__, 3) . $this->config->getDefinitionDirectory();
        if (false === file_exists($definitionsPath)) {
            throw new DefinitionsFileNotFoundException($definitionsPath);
        }

        $definitions = require $definitionsPath;
        if (false === is_array($definitions)) {
            throw new InvalidDefinitionsFormatException($definitionsPath);
        }

        foreach ($definitions as $definition) {
            $reflectionClass = new ReflectionClass($definition);
            $this->uploadProvider($reflectionClass->newInstance());
        }
    }
}