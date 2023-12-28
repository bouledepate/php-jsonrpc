<?php

namespace JRPC\Kernel\Command\Data;

interface DtoCollectorInterface
{
    public function collectDTO(string $dtoClass, array|string $parameters): CommandDTO;
}