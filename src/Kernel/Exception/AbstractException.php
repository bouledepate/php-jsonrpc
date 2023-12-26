<?php

declare(strict_types=1);

namespace Kernel\Exception;

use Exception;
use Throwable;

abstract class AbstractException extends Exception
{
    protected $message = 'Unexpected exception occurred.';
    protected string|array|null $detail = null;

    public function __construct(ExceptionCode|int|null $code = null, ?string $message = null, ?Throwable $previous = null)
    {
        if ($code === null) {
            $code = $this->getCode() ?? ExceptionCode::UNEXPECTED;
        }
        parent::__construct(
            message: $message === null ? $this->getMessage() : $message,
            code: $code instanceof ExceptionCode ? $code->value : $code,
            previous: $previous
        );
    }

    public function getDetail(): array|string|null
    {
        return $this->detail;
    }
}