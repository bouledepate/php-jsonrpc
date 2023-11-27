<?php

declare(strict_types=1);

namespace WoopLeague\Application\Example;

use WoopLeague\Kernel\Command\Command;
use WoopLeague\Kernel\Command\CommandHandler;
use WoopLeague\Kernel\Command\CommandResponse;

#[Command(name: 'example', dtoClass: ExampleRequest::class)]
final class ExampleHandler extends CommandHandler
{
    public function __construct(
        private readonly ExampleService   $service,
        private readonly ExampleInterface $processor
    )
    {
    }

    public function handle(): CommandResponse
    {
        /** @var ExampleRequest $dto */
        $dto = $this->getDto();
        $message = $this->service->testAction($dto);
        return new CommandResponse([
            'message' => $message,
            'data' => $this->processor->fetch($dto)
        ]);
    }
}