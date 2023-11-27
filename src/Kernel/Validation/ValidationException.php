<?php

declare(strict_types=1);

namespace Kernel\Validation;

use Kernel\Error\AbstractException;
use Kernel\Error\Error;

final class ValidationException extends AbstractException
{
    protected $code = Error::VALIDATION_FAILED;
    protected $message = "Validation failed.";

    public function __construct(protected ?array $details = null)
    {
        $this->detail = $details;
        parent::__construct();
    }
}