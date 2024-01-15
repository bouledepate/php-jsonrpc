<?php

use Application\Development\Components\TokenMiddleware;

/**
 * This file is intended for the registration of custom middlewares in your application.
 *
 * Each middleware must implement the Middleware interface. Middlewares are responsible for
 * processing incoming requests before they reach your application's core handling. They provide
 * a convenient way to insert layers of processing, such as authentication, logging, or other request handling.
 *
 * To register a middleware, simply include it in this configuration file. For example, if you have
 * a custom logging middleware, you would register it like this:
 *
 * return [
 *     \Your\Application\LoggingMiddleware::class,
 * ];
 */
return [
    TokenMiddleware::class,
];