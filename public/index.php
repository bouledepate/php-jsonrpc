<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use JRPC\Kernel\KernelFactory;
use JRPC\Kernel\Exception\Handler\ShutdownHandler;

try {
    $application = KernelFactory::buildApplication();
    $application->run();
} catch (Throwable $exception) {
    ShutdownHandler::handleException($exception);
}