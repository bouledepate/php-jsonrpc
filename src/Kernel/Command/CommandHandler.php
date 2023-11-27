<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Command;

use WoopLeague\Kernel\Data\AbstractDTO;

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