<?php

declare(strict_types=1);

namespace WoopLeague\Application\Example;

interface ExampleInterface
{
    public function fetch(ExampleRequest $request): array;
}