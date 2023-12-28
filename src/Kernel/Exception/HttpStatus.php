<?php

declare(strict_types=1);

namespace JRPC\Kernel\Exception;

enum HttpStatus: int
{
    case HTTP_SUCCESS = 200;
    case HTTP_CREATED = 201;
    case HTTP_NO_CONTENT = 204;
    case HTTP_BAD_REQUEST = 400;
    case HTTP_UNAUTHORIZED = 401;
    case HTTP_FORBIDDEN = 403;
    case HTTP_NOT_FOUND = 404;
    case HTTP_METHOD_NOT_ALLOWED = 405;
    case HTTP_SERVER_ERROR = 500;
}