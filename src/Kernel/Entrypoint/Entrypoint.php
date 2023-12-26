<?php

declare(strict_types=1);

namespace Kernel\Entrypoint;

use Kernel\Command\Contract\CommandRequest;
use Kernel\Command\Contract\Method;
use Kernel\Command\Interfaces\CommandDispatcher;
use Kernel\Exception\JRPC\InternalErrorException;
use Kernel\Exception\JRPC\InvalidParamsException;
use Kernel\Exception\JRPC\MethodNotFound;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class Entrypoint implements EntrypointController
{
    public function __construct(
        private ResponseFactoryInterface   $factory,
        private CommandDispatcher $dispatcher
    )
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $commandRequest = $this->collectCommandRequest($request);
        $commandResponse = $this->dispatcher->dispatch($commandRequest);

        $psrResponse = $this->factory->createResponse();
        $psrResponse->getBody()->write($commandResponse->getSerializedData());

        return $psrResponse;
    }

    private function collectCommandRequest(ServerRequestInterface $request): CommandRequest
    {
        $requestData = $request->getParsedBody();
        return new CommandRequest(new Method($requestData['method']), $requestData['params'] ?? []);
    }
}