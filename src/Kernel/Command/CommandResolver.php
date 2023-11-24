<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Command;

use WoopLeague\Kernel\Error\JsonRpc\InternalErrorException;
use WoopLeague\Kernel\Error\JsonRpc\MethodNotFound;

final class CommandResolver
{
    private static array $commands = [];

    /**
     * @throws InternalErrorException
     */
    private static function loadCommands(): void
    {
        if (!empty(self::$commands)) {
            return;
        }

        $commandsConfig = dirname(__DIR__, 2) . '/Application/Config/commands.php';
        if (file_exists($commandsConfig)) {
            $commands = require_once $commandsConfig;
            if ($commands !== 1) {
                self::$commands = $commands;
            }
        }
    }

    /**
     * @throws MethodNotFound|InternalErrorException
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