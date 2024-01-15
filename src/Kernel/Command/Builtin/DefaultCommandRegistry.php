<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Builtin;

use JRPC\Kernel\Command\BaseCommand;
use JRPC\Kernel\Command\Command;
use JRPC\Kernel\Command\Exception\ConfigurationNotFoundException;
use JRPC\Kernel\Command\Exception\InvalidConfigurationFileException;
use JRPC\Kernel\Command\Exception\MissingCommandAttribute;
use JRPC\Kernel\Command\Interfaces\CommandProvider;
use JRPC\Kernel\Command\Interfaces\CommandRegistry;
use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Exception\JRPC\MethodNotFound;
use ReflectionClass;
use ReflectionException;

class DefaultCommandRegistry implements CommandRegistry
{
    /** @var Command[] */
    private array $commands = [];

    /**
     * @throws MissingCommandAttribute
     * @throws ReflectionException
     * @throws InvalidConfigurationFileException
     * @throws ConfigurationNotFoundException
     */
    public function __construct(private readonly ApplicationConfig $config)
    {
        $this->uploadCommands();
    }

    /**
     * @throws MethodNotFound
     */
    public function fetchCommandBy(string $method): string
    {
        return $this->getCommand($method)->getHandler();
    }

    /**
     * @throws MethodNotFound
     */
    public function isDtoRequiredFor(string $method): bool
    {
        return $this->getCommand($method)->getDTO() !== null;
    }

    /**
     * @throws MethodNotFound
     */
    public function fetchDtoBy(string $method): ?string
    {
        return $this->getCommand($method)->getDTO();
    }

    /**
     * @throws MethodNotFound
     */
    private function getCommand(string $method): Command
    {
        if (!array_key_exists($method, $this->commands)) {
            throw new MethodNotFound();
        }
        return $this->commands[$method];
    }

    /**
     * @throws ConfigurationNotFoundException
     * @throws InvalidConfigurationFileException
     * @throws ReflectionException
     * @throws MissingCommandAttribute
     */
    protected function uploadCommands(): void
    {
        $configPath = $this->config->getRootPath() . $this->config->getCommandsDirectory();
        if (false === file_exists($configPath)) {
            throw new ConfigurationNotFoundException($configPath);
        }

        $providers = require $configPath;
        if (false === is_array($providers)) {
            throw new InvalidConfigurationFileException($configPath);
        }

        foreach ($providers as $provider) {
            /** @var CommandProvider $provider */
            $provider = (new ReflectionClass($provider))->newInstance();

            if (!$provider instanceof CommandProvider) {
                continue;
            }

            foreach ($provider->commands() as $command) {
                $commandReflection = new ReflectionClass($command);

                if (!$commandReflection->isSubclassOf(BaseCommand::class)) {
                    continue;
                }

                $attributes = $commandReflection->getAttributes(Command::class);
                $cmdAttribute = array_pop($attributes);

                if (null === $cmdAttribute) {
                    throw new MissingCommandAttribute($commandReflection->getName());
                }

                /** @var Command $command */
                $command = $cmdAttribute->newInstance();
                $command->setHandler($commandReflection->getName());
                $this->commands[$command->getName()] = $command;
            }
        }
    }
}