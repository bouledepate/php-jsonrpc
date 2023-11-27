<?php

declare(strict_types=1);

namespace WoopLeague\Kernel\Entrypoint;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;
use WoopLeague\Kernel\Command\CommandDispatcher;
use WoopLeague\Kernel\Command\CommandRequest;
use WoopLeague\Kernel\Error\JsonRpc\InternalErrorException;
use WoopLeague\Kernel\Error\JsonRpc\InvalidParamsException;
use WoopLeague\Kernel\Error\JsonRpc\MethodNotFound;
use WoopLeague\Kernel\Validation\ValidationException;

readonly class Entrypoint implements EntrypointController
{
    public function __construct(
        private ResponseFactoryInterface $factory,
        private CommandDispatcher        $dispatcher
    )
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws InternalErrorException
     * @throws MethodNotFound
     * @throws NotFoundExceptionInterface
     * @throws ValidationException
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $commandRequest = $this->collectCommandRequest($request);
        $commandResponse = $this->dispatcher->dispatch($commandRequest);

        $psrResponse = $this->factory->createResponse();
        $psrResponse->getBody()->write(json_encode($commandResponse->getData(), JSON_UNESCAPED_SLASHES));

        return $psrResponse;
    }

    private function collectCommandRequest(ServerRequestInterface $request): CommandRequest
    {
        $requestData = $request->getParsedBody();
        return new CommandRequest($requestData['method'], $requestData['params'] ?? []);
    }
}