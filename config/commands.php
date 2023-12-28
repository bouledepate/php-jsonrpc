<?php

/**
 * This file contains the command providers for your application. To register a new command provider,
 * you need to create a class that implements the CommandProvider interface and list it in this file.
 *
 * A command provider is responsible for providing the details of commands available in the application.
 * This approach simplifies the management of commands and their handlers. Instead of listing all commands
 * individually, you only need to specify the command providers here.
 *
 * For example, to add a new set of commands, you would include the provider class like this:
 *
 * return [
 *     \Your\Application\CommandList::class
 * ];
 */
return [
    Application\Development\DevelopmentCommandList::class,
];