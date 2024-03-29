<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Builtin;

use JRPC\Kernel\Command\BaseCommand;
use JRPC\Kernel\Command\Contract\CommandRequest;
use JRPC\Kernel\Command\Contract\CommandResponse;
use JRPC\Kernel\Command\Data\DtoCollectorInterface;
use JRPC\Kernel\Command\Exception\CommandNotInstantiated;
use JRPC\Kernel\Command\Interfaces\CommandDispatcher;
use JRPC\Kernel\Command\Interfaces\CommandInterface;
use JRPC\Kernel\Command\Interfaces\CommandRegistry;
use JRPC\Kernel\Exception\JRPC\MethodNotFound;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class DefaultCommandDispatcher implements CommandDispatcher
{
    public function __construct(
        private CommandRegistry       $commandRegistry,
        private DtoCollectorInterface $dtoFactory,
        private SerializerInterface   $serializer,
        private ContainerInterface    $container
    )
    {
    }

    /**
     * @param CommandRequest $commandRequest
     * @return CommandResponse
     * @throws CommandNotInstantiated
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws MethodNotFound
     */
    public function dispatch(CommandRequest $commandRequest): CommandResponse
    {
        $method = $commandRequest->getMethod()->getValue();
        $commandClass = $this->commandRegistry->fetchCommandBy($method);

        if (false === $this->container->has($commandClass)) {
            throw new CommandNotInstantiated($commandClass);
        }

        $command = $this->collectCommand($commandClass, $commandRequest);
        $command->execute();

        return new CommandResponse(
            data: $this->serializer->serialize($command->getResult(), 'json')
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws MethodNotFound
     */
    private function collectCommand(string $class, CommandRequest $commandRequest): CommandInterface
    {
        $method = $commandRequest->getMethod()->getValue();

        /** @var BaseCommand $command */
        $command = $this->container->get($class);
        $command->setContext($commandRequest);

        if ($this->commandRegistry->isDtoRequiredFor($method)) {
            $dto = $this->dtoFactory->collectDTO(
                dtoClass: $this->commandRegistry->fetchDtoBy($method),
                parameters: $commandRequest->getParameters()->getValueAsString()
            );
            $command->setPayload($dto);
        }

        return $command;
    }
}