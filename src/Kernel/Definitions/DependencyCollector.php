<?php

declare(strict_types=1);

namespace Kernel\Definitions;

use ReflectionClass;
use ReflectionException;
use Kernel\Config\DependencyProvider;
use Kernel\KernelDefinitions;

final class DependencyCollector
{
    /**
     * @param array|DependencyProvider[] $providers
     * @throws DefinitionsFileNotFoundException
     * @throws InvalidDefinitionsFormatException
     * @throws ReflectionException
     */
    public function __construct(private array $providers = [])
    {
        // Kernel definitions must be included.
        $this->uploadProvider(new KernelDefinitions());
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
        $definitionsPath = dirname(__DIR__, 3) . $_ENV['DEFINITIONS_CONFIG'];
        if (file_exists($definitionsPath) === false) {
            throw new DefinitionsFileNotFoundException($definitionsPath);
        }
        $definitions = require $definitionsPath;
        if (is_array($definitions) === false) {
            throw new InvalidDefinitionsFormatException($definitionsPath);
        }
        foreach ($definitions as $definition) {
            $reflectionClass = new ReflectionClass($definition);
            $this->uploadProvider($reflectionClass->newInstance());
        }
    }
}