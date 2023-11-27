<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Data\AbstractDTO;

abstract class CommandHandler
{
    private ?AbstractDTO $DTO = null;

    abstract public function handle(): CommandResponse;

    public function uploadDTO(AbstractDTO $DTO): void
    {
        $this->DTO = $DTO;
    }

    public function getDto(): ?AbstractDTO
    {
        return $this->DTO;
    }
}