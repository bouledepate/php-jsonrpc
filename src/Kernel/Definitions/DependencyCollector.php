<?php

declare(strict_types=1);

namespace JRPC\Kernel\Definitions;

use JRPC\Kernel\KernelConfig;
use JRPC\Kernel\KernelDefinitions;
use ReflectionClass;
use ReflectionException;

final class DependencyCollector
{
    private array $providers = [];

    /**
     * @throws DefinitionsFileNotFoundException
     * @throws InvalidDefinitionsFormatException
     * @throws ReflectionException
     */
    public function __construct(private readonly KernelConfig $config)
    {
        // Kernel definitions must be included.
        $this->uploadProvider(new KernelDefinitions($this->config));
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
     * @throws DefinitionsFileNotFoundException
     * @throws InvalidDefinitionsFormatException
     * @throws ReflectionException
     */
    private function loadProvidersFromConfiguration(): void
    {
        $root = $this->config->getMainConfig()->getRootPath();
        $definitions = $this->config->getMainConfig()->getDefinitionDirectory();

        $definitionsPath = $root . $definitions;
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