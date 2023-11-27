<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Kernel\Error\AbstractException;
use Kernel\KernelFactory;

try {
    $application = KernelFactory::buildApplication();
    $application->run();
} catch (Throwable $exception) {
    header('Content-Type: application/json', true, 500);
    $message = [
        'jsonrpc' => '2.0',
        'error' => [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ],
        'id' => null
    ];

    if ($exception instanceof AbstractException) {
        $message['error']['data'] = $exception->getDetail();
    }

    echo json_encode($message, JSON_UNESCAPED_SLASHES);
    exit;
}