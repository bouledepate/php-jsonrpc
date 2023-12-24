<?php

declare(strict_types=1);

namespace Kernel\Command\Contract;

use Kernel\ValueObject\StringValueObject;
use Symfony\Component\String\UnicodeString;


final class Method extends StringValueObject
{
    private const string SEPARATOR = '.';

    public function getCommand(): string
    {
        $methodParts = $this->split();
        $command = new UnicodeString((string)array_pop($methodParts));

        return $command->camel()->title(true)->toString();
    }

    public function getScope(): string
    {
        $methodParts = $this->split();
        $scope =  new UnicodeString((string)array_shift($methodParts));

        return $scope->snake()->title(true)->toString();
    }

    private function split(): array
    {
        $value = $this->getValue();
        return $value->split(self::SEPARATOR);
    }
}