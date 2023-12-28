<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception;

final class MiddlewaresFileNotFoundException extends AbstractException
{
    protected $code = ExceptionCode::MIDDLEWARES_CONFIG_NOT_FOUND;

    public function __construct($path)
    {
        $message = "Configuration file not found at '{$path}'.";
        parent::__construct($this->code, $message);
    }
}