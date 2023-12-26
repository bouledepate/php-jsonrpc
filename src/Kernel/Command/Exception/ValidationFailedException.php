<?php

declare(strict_types=1);

namespace Kernel\Command\Exception;

use Kernel\Exception\AbstractException;
use Kernel\Exception\ExceptionCode;

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