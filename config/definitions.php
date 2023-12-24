<?php

use WoopLeague\Application;

/**
 * This file is intended to list all the definition providers of your application.
 *
 * Each provider must implement the DependencyProvider interface. Providers are responsible for
 * defining dependencies that your application requires. By registering a provider, you ensure
 * that these dependencies are appropriately managed and injected where necessary.
 *
 * For instance, suppose you have an AccountProvider responsible for account-related dependencies.
 * You can register this provider by including it in this configuration file as shown below:
 *
 * return [
 *     \Your\Application\AccountProvider::class,
 * ];
 */
return [
    Application\Definitions::class,
];