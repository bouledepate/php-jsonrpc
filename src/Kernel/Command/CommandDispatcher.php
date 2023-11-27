<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use WoopLeague\Kernel\Data\RequestDtoFactory;
use WoopLeague\Kernel\Error\JsonRpc\InternalErrorException;
use WoopLeague\Kernel\Error\JsonRpc\InvalidParamsException;
use WoopLeague\Kernel\Error\JsonRpc\MethodNotFound;
use WoopLeague\Kernel\Validation\ValidationException;

final readonly class CommandDispatcher
{
    public function __construct(
        private CommandResolver    $commandResolver,
        private RequestDtoFactory  $dtoFactory,
        private ContainerInterface $container
    )
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws InternalErrorException
     * @throws InvalidParamsException
     * @throws MethodNotFound
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ValidationException
     */
    public function dispatch(CommandRequest $request): CommandResponse
    {
        $handlerClass = $this->commandResolver->resolve($request->getMethod());
        if (!class_exists($handlerClass)) {
            throw new MethodNotFound("Method '{$request->getMethod()}' does not exist.");
        }

        if (!$this->container->has($handlerClass)) {
            throw new InternalErrorException("Command handler '$handlerClass' cannot be instantiated.");
        }

        $handler = $this->container->get($handlerClass);
        if (!$handler instanceof CommandHandler) {
            throw new InternalErrorException("Resolved command handler '$handlerClass' does not extend the required CommandHandler class.");
        }

        $this->uploadDTO($handler, $request->getParameters());
        return $handler->handle();
    }

    /**
     * @throws InternalErrorException
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws ValidationException
     */
    private function uploadDTO(CommandHandler $handler, array $data = []): void
    {
        $reflectionClass = new ReflectionClass($handler);
        $attributes = $reflectionClass->getAttributes(Command::class);
        if (count($attributes) === 0) {
            throw new InternalErrorException("Command attribute is not defined on the handler class.");
        }

        $commandAttribute = $attributes[0]->newInstance();
        if (!$commandAttribute->isDtoRequired()) {
            return;
        }

        $dtoClass = $commandAttribute->getDtoClass();
        if ($dtoClass === null) {
            throw new InternalErrorException("DTO class does not defined.");
        }
        if (!class_exists($dtoClass)) {
            throw new InternalErrorException("DTO class does not exist.");
        }

        $dto = $this->dtoFactory->produce($dtoClass, $data);
        $handler->uploadDTO($dto);
    }
}