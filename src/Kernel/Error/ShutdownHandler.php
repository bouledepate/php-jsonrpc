<?php

declare(strict_types=1);

namespace Kernel\Error;

use Throwable;

final readonly class ShutdownHandler
{
    private const int HTTP_INTERNAL_ERROR = 500;

    public static function handleException(Throwable $exception): never
    {
        header('Content-Type: application/json', true, self::HTTP_INTERNAL_ERROR);
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