<?php

declare(strict_types=1);

namespace WoopLeague\Application\Example;

final readonly class ExampleService
{
    public function testAction(ExampleRequest $request): string
    {
        $message = "Hello, {$request->getUsername()}. ";
        if ($request->getPassword()) {
            $message .= "Your password is: qwerty123.";
        }
        return $message;
    }
}