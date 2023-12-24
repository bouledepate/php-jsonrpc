<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Command\Contract\Method;
use Kernel\Command\Exception\CommandNotRegistered;
use Kernel\Command\Exception\ConfigurationNotFoundException;
use Kernel\Command\Exception\MissingCommandAttribute;
use Kernel\Command\Exception\InvalidConfigurationFileException;
use Kernel\Command\Interfaces\CommandHandler;
use Kernel\Command\Interfaces\CommandProvider;
use Kernel\Command\Interfaces\CommandRegistry;
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
    public function __construct()
    {
        $this->uploadCommands();
    }

    public function isCommandExist(Method $method): bool
    {
        return array_key_exists((string)$method, $this->commands);
    }

    public function fetchHandlerBy(Method $method): string
    {
        return $this->getCommand($method)->getHandler();
    }

    public function isDtoRequiredFor(Method $method): bool
    {
        return $this->getCommand($method)->getDTO() !== null;
    }

    public function fetchDtoBy(Method $method): ?string
    {
        return $this->getCommand($method)->getDTO();
    }

    /**
     * @throws CommandNotRegistered
     */
    private function getCommand(Method $method): Command
    {
        $methodName = (string)$method;
        if (!array_key_exists($methodName, $this->commands)) {
            throw new CommandNotRegistered($method->getValue()->toString());
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
        $configPath = dirname(__DIR__, 3) . $_ENV['COMMANDS_CONFIG'];
        if (file_exists($configPath) === false) {
            throw new ConfigurationNotFoundException($configPath);
        }

        $providers = require $configPath;
        if (is_array($providers) === false) {
            throw new InvalidConfigurationFileException($configPath);
        }

        foreach ($providers as $provider) {
            /** @var CommandProvider $provider */
            $provider = (new ReflectionClass($provider))->newInstance();

            if (!$provider instanceof CommandProvider) {
                continue;
            }

            foreach ($provider->commands() as $handler) {
                $handler = new ReflectionClass($handler);
                $handlerInstance = $handler->newInstance();

                if (!$handlerInstance instanceof CommandHandler) {
                    continue;
                }

                $attributes = $handler->getAttributes(Command::class);
                $commandAttribute = array_pop($attributes);

                if ($commandAttribute === null) {
                    throw new MissingCommandAttribute($handlerInstance::class);
                }

                /** @var Command $command */
                $command = $commandAttribute->newInstance();
                $command->setHandler($handlerInstance::class);
                $this->commands[$command->getName()] = $command;
            }
        }
    }
}