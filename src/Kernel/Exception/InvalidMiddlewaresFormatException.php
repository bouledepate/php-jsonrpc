<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception;

final class InvalidMiddlewaresFormatException extends AbstractException
{
    protected $code = ExceptionCode::MIDDLEWARES_CONFIG_INVALID_FILE;

    public function __construct(string $path)
    {
        $message = "Invalid format in configuration file at '$path'.";
        parent::__construct($this->code, $message);
    }
}