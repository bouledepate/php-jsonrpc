<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Error\JsonRpc\InternalErrorException;
use Kernel\Error\JsonRpc\MethodNotFound;

final class CommandResolver
{
    private static array $commands = [];

    /**
     * @throws InternalErrorException
     * @throws CommandsFileInvalidFormatException
     * @throws CommandsFileNotFoundException
     */
    private static function loadCommands(): void
    {
        if (!empty(self::$commands)) {
            return;
        }
        $commandsConfig = dirname(__DIR__, 3) . $_ENV['COMMANDS_CONFIG'];
        if (file_exists($commandsConfig)) {
            $commands = require_once $commandsConfig;
            if ($commands !== 1) {
                self::$commands = $commands;
            } else {
                throw new CommandsFileInvalidFormatException($commandsConfig);
            }
        } else {
            throw new CommandsFileNotFoundException($commandsConfig);
        }
    }

    /**
     * @throws CommandsFileInvalidFormatException
     * @throws CommandsFileNotFoundException
     * @throws InternalErrorException
     * @throws MethodNotFound
     */
    public static function resolve(string $command): string
    {
        self::loadCommands();

        if (!array_key_exists($command, self::$commands)) {
            throw new MethodNotFound("Requested command '$command' not found.");
        }

        return self::$commands[$command];
    }
}