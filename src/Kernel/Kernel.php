<?php

declare(strict_types=1);

namespace JRPC\Kernel;

use JRPC\Kernel\Entrypoint\EntrypointController;
use JRPC\Kernel\Entrypoint\EntrypointNotSetException;
use JRPC\Kernel\Exception\Handler\ExceptionHandler;
use JRPC\Kernel\Middlewares\ComplianceMiddleware;
use JRPC\Kernel\Middlewares\ContextMiddleware;
use JRPC\Kernel\Middlewares\ValidationMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\App;

final readonly class Kernel
{
    public function __construct(
        private App                $application,
        private KernelConfig       $config,
        private ContainerInterface $container,
        private ?array             $middlewares = []
    )
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws EntrypointNotSetException
     */
    public function setup(): Kernel
    {
        $this->connectMiddlewares();
        $this->applyEntrypoint();

        return $this;
    }

    public function run(): void
    {
        $this->application->run();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function connectMiddlewares(): void
    {
        // JsonRPC validation middlewares.
        $this->application->add($this->container->get(ComplianceMiddleware::class));
        $this->application->add($this->container->get(ValidationMiddleware::class));
        $this->application->add($this->container->get(ContextMiddleware::class));

        $this->application->addBodyParsingMiddleware();

        $configuration = $this->config->getMainConfig();

        $errorMiddleware = $this->application->addErrorMiddleware(
            displayErrorDetails: $configuration->isDisplayErrorDetails(),
            logErrors: $configuration->isLogErrors(),
            logErrorDetails: $configuration->isLogErrorDetails()
        );

        $errorHandler = new ExceptionHandler(
            $this->application->getCallableResolver(),
            $this->application->getResponseFactory()
        );

        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        // Custom middlewares.
        foreach ($this->middlewares as $middleware) {
            $this->application->add($middleware);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws EntrypointNotSetException
     */
    private function applyEntrypoint(): void
    {
        if (false === $this->container->has(EntrypointController::class)) {
            throw new EntrypointNotSetException();
        }
        $this->application->post(
            pattern: $this->config->getJrpcConfig()->getEntrypoint(),
            callable: $this->container->get(EntrypointController::class)
        );
    }
}