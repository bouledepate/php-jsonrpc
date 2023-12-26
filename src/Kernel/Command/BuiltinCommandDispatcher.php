<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Command\Contract\CommandRequest;
use Kernel\Command\Contract\CommandResponse;
use Kernel\Command\Exception\CommandHandlerNotInstantiated;
use Kernel\Command\Interfaces\CommandDispatcher;
use Kernel\Command\Interfaces\CommandDTOFactory;
use Kernel\Command\Interfaces\CommandRegistry;
use Kernel\Exception\JRPC\MethodNotFound;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class BuiltinCommandDispatcher implements CommandDispatcher
{
    public function __construct(
        private CommandRegistry     $commandRegistry,
        private CommandDTOFactory   $dtoFactory,
        private SerializerInterface $serializer,
        private ContainerInterface  $container
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

        /** @var */
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