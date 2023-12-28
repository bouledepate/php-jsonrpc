<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Exception;

use JRPC\Kernel\Exception\AbstractException;
use JRPC\Kernel\Exception\ExceptionCode;

final class ValidationFailedException extends AbstractException
{
    protected $code = ExceptionCode::VALIDATION_FAILED;
    protected $message = "Validation failed.";

    public function __construct(?array $errors = null)
    {
        if ($errors !== null) {
            $this->detail = $errors;
        }
        parent::__construct();
    }
}