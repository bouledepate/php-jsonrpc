<?php

declare(strict_types=1);

namespace JRPC\Kernel\Entrypoint;

use JRPC\Kernel\Command\Contract\CommandRequest;
use JRPC\Kernel\Command\Interfaces\CommandDispatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        return new CommandRequest($requestData['method'], $requestData['params'] ?? []);
    }
}