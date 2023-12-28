<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Data;

interface DtoValidatorInterface
{
    public function validate(CommandDTO $DTO): void;

    public function isValid(): bool;

    public function getErrors(): array;
}