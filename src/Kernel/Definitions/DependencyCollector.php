<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Definitions;

use ReflectionClass;
use ReflectionException;
use WoopLeague\Kernel\Command\Command;
use WoopLeague\Kernel\Command\CommandProvider;
use WoopLeague\Kernel\Config\DependencyProvider;
use WoopLeague\Kernel\KernelDefinitions;

final class DependencyCollector
{
    /**
     * @param array|DependencyProvider[] $providers
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

            // Need to register commands if exists.
            if ($provider instanceof CommandProvider) {
                $commands = $provider->commands();

                // Remove all definitions if it is not the command.
                array_walk($commands, static function ($value, string $key) use (&$commands) {
                    $reflectionClass = new ReflectionClass($key);
                    $attribute = $reflectionClass->getAttributes(Command::class)[0] ?? null;
                    if ($attribute === null) {
                        unset($commands[$key]);
                    }
                });

                $definitions = array_merge($definitions, $commands);
            }
        }
        return $definitions;
    }

    public function uploadProvider(DependencyProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @throws ReflectionException
     */
    private function loadProvidersFromConfiguration(): void
    {
        $definitionsPath = dirname(__DIR__, 3) . '/config/definitions.php';
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