<?php

declare(strict_types=1);

namespace Kernel\Exception;

use Throwable;

final readonly class ShutdownHandler
{
    public static function handleException(Throwable $exception): never
    {
        header('Content-Type: application/json', true, HttpStatus::HTTP_SERVER_ERROR->value);
        $response = self::formatException($exception);
        if ($exception instanceof AbstractException) {
            $response['error']['data'] = $exception->getDetail();
        }
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit;
    }

    private static function formatException(Throwable $exception): array
    {
        return [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ],
            'id' => null
        ];
    }
}