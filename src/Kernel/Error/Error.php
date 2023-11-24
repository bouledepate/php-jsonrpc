<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Error;

enum Error: int
{
    case UNEXPECTED = -1;
    case VALIDATION_FAILED = -2;
    case TYPECAST_FAILED = -3;
}