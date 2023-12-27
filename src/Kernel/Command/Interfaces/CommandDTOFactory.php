<?php

declare(strict_types=1);

namespace Kernel\Command\Interfaces;

interface CommandDTOFactory
{
    public function collectDTO(string $dtoClass, string $params): CommandDTO;
}