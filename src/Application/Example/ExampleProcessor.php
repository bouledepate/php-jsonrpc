<?php

declare(strict_types=1);

namespace WoopLeague\Application\Example;

class ExampleProcessor implements ExampleInterface
{
    public function fetch(ExampleRequest $request): array
    {
        return $request->getObject() ?? [];
    }
}