<?php

declare(strict_types=1);

namespace Kernel\Command;

use Kernel\Command\Contract\CommandRequest;
use Kernel\Command\Contract\CommandResponse;
use Kernel\Command\Exception\CommandHandlerNotInstantiated;
use Kernel\Command\Exception\CommandNotRegistered;
use Kernel\Command\Interfaces\CommandDispatcher;
use Kernel\Command\Interfaces\CommandDTO;
use Kernel\Command\Interfaces\CommandHandler;
use Kernel\Command\Interfaces\CommandRegistry;
use Kernel\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class BuiltinCommandDispatcher implements CommandDispatcher
{
    public function __construct(
        private CommandRegistry     $commandRegistry,
        private SerializerInterface $serializer,
        private ContainerInterface  $container
    )
    {
    }

    /**
     * @param CommandRequest $commandRequest
     * @return CommandResponse
     * @throws CommandNotRegistered
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws CommandHandlerNotInstantiated
     * @throws ValidationException
     */
    public function dispatch(CommandRequest $commandRequest): CommandResponse
    {
        $method = $commandRequest->getMethod();
        $handlerClass = $this->commandRegistry->fetchHandlerBy($method);

        if (!$this->container->has($handlerClass)) {
            throw new CommandHandlerNotInstantiated($handlerClass);
        }

        /** @var CommandHandler $handler */
        $handler = $this->container->get($handlerClass);

        $parameters = $commandRequest->getParameters();
        $dto = null;

        if ($this->commandRegistry->isDtoRequiredFor($method)) {
            $dtoClass = $this->commandRegistry->fetchDtoBy($method);
            if ($dtoClass !== null) {
                $dto = $this->serializer->deserialize($parameters, $dtoClass, 'json', [
                    DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true
                ]);
            }
        }

        $result = $handler->handle($dto);
        $responseData = $this->serializer->serialize($result, 'json');

        return new CommandResponse($responseData);
    }

    /** @throws ValidationException */
    private function collectDTO(string $dtoClass, string $parameters): CommandDTO
    {
        try {

        } catch (PartialDenormalizationException $e) {
            $errors = [];
            foreach ($e->getErrors() as $exception) {
                $a = 1;
            }
            throw new ValidationException($errors);
        }
        return $dto;
    }
}