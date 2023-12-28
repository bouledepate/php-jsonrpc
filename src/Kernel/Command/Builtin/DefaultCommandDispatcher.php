<?php

declare(strict_types=1);

namespace JRPC\Kernel\Command\Builtin;

use JRPC\Kernel\Command\Contract\CommandRequest;
use JRPC\Kernel\Command\Contract\CommandResponse;
use JRPC\Kernel\Command\Data\DtoCollectorInterface;
use JRPC\Kernel\Command\Exception\CommandHandlerNotInstantiated;
use JRPC\Kernel\Command\Interfaces\CommandDispatcher;
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
     * @throws CommandHandlerNotInstantiated
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws MethodNotFound
     */
    public function dispatch(CommandRequest $commandRequest): CommandResponse
    {
        $method = $commandRequest->getMethod();
        $commandHandler = $this->commandRegistry->fetchHandlerBy($method);

        if (!$this->container->has($commandHandler)) {
            throw new CommandHandlerNotInstantiated($commandHandler);
        }

        $commandHandler = $this->container->get($commandHandler);
        $parameters = $commandRequest->getParameters();

        if ($this->commandRegistry->isDtoRequiredFor($method)) {
            $dtoClass = $this->commandRegistry->fetchDtoBy($method);
            if ($dtoClass !== null) {
                $dto = $this->dtoFactory->collectDTO($dtoClass, $parameters);
                $commandHandler->setPayload($dto);
            }
        }

        $commandHandler->execute();
        $responseData = $this->serializer->serialize($commandHandler->getResult(), 'json');
        return new CommandResponse($responseData);
    }
}