<?php

use WoopLeague\Application\Example;

/**
 * This file contains all commands/methods that exist in your application. To register a new method, you need to create
 * a command handler and place its class name into this file.
 *
 * For example, if you have created a new method named 'account.createAccount', the next step is to add this method name
 * and its corresponding command handler class to the array like:
 *
 * return [
 *    'account.createAccount' => \Your\Application\Namespace\CommandHandler::class
 * ];
 */
return [
    'example' => Example\ExampleHandler::class,
];