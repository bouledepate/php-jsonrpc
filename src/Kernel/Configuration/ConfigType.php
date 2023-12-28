<?php

declare(strict_types=1);

namespace JRPC\Kernel\Configuration;

enum ConfigType: string
{
    case Application = 'application_config';
    case JsonRpc = 'json_rpc_config';
}