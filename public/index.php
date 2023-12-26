<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Kernel\Exception\ShutdownHandler;
use Kernel\KernelFactory;

try {
    $application = KernelFactory::buildApplication();
    $application->run();
} catch (Throwable $exception) {
    ShutdownHandler::handleException($exception);
}