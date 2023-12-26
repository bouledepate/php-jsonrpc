<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Command\Contract\Method;
use Kernel\Command\Exception\ConfigurationNotFoundException;
use Kernel\Command\Exception\MissingCommandAttribute;
use Kernel\Command\Exception\InvalidConfigurationFileException;
use Kernel\Command\Command;
use Kernel\Command\Interfaces\Command as CommandInterface;
use Kernel\Command\Interfaces\CommandProvider;
use Kernel\Command\Interfaces\CommandRegistry;
use Kernel\Configuration\ApplicationConfig;
use Kernel\Exception\JRPC\MethodNotFound;
use ReflectionClass;
use ReflectionException;

class BuiltinCommandRegistry implements CommandRegistry
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

    public function isCommandExist(Method $method): bool
    {
        return array_key_exists((string)$method, $this->commands);
    }

    /**
     * @throws MethodNotFound
     */
    public function fetchHandlerBy(Method $method): string
    {
        return $this->getCommand($method)->getHandler();
    }

    /**
     * @throws MethodNotFound
     */
    public function isDtoRequiredFor(Method $method): bool
    {
        return $this->getCommand($method)->getDTO() !== null;
    }

    /**
     * @throws MethodNotFound
     */
    public function fetchDtoBy(Method $method): ?string
    {
        return $this->getCommand($method)->getDTO();
    }

    /**
     * @throws MethodNotFound
     */
    private function getCommand(Method $method): Command
    {
        $methodName = (string)$method;
        if (!array_key_exists($methodName, $this->commands)) {
            throw new MethodNotFound();
        }
        return $this->commands[$methodName];
    }

    /**
     * @throws ConfigurationNotFoundException
     * @throws InvalidConfigurationFileException
     * @throws ReflectionException
     * @throws MissingCommandAttribute
     */
    protected function uploadCommands(): void
    {
        $configPath = dirname(__DIR__, 3) . $this->config->getCommandsDirectory();
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