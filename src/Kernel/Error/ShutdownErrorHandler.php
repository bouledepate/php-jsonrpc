<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Error;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;

final readonly class ShutdownErrorHandler
{
    public function __construct(
        private ServerRequestInterface $request,
        private ErrorHandler           $errorHandler,
        private bool                   $displayErrorDetails
    )
    {
    }

    public function __invoke(): void
    {
        $error = error_get_last();
        if ($error) {
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                $message = match ($errorType) {
                    E_USER_ERROR => "FATAL ERROR: {$errorMessage}. ",
                    E_USER_WARNING => "WARNING: {$errorMessage}",
                    E_USER_NOTICE => "NOTICE: {$errorMessage}",
                    default => "ERROR: {$errorMessage}",
                };
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, false, false);

            if (ob_get_length()) {
                ob_clean();
            }

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}