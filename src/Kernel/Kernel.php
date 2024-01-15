<?php

declare(strict_types=1);

namespace JRPC\Kernel;

use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Configuration\JsonRpcConfig;
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
    private ApplicationConfig $config;
    private JsonRpcConfig $jrpcConfig;

    public function __construct(
        private App                $application,
        KernelConfig               $config,
        private ContainerInterface $container,
        private ?array             $middlewares = []
    )
    {
        $this->config = $config->getMainConfig();
        $this->jrpcConfig = $config->getJrpcConfig();
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
        // Custom middlewares.
        foreach ($this->middlewares as $middleware) {
            $this->application->add($middleware);
        }

        // JsonRPC validation middlewares.
        $this->application->add($this->container->get(ComplianceMiddleware::class));
        $this->application->add($this->container->get(ValidationMiddleware::class));
        $this->application->add($this->container->get(ContextMiddleware::class));

        $this->application->addBodyParsingMiddleware();

        $errorMiddleware = $this->application->addErrorMiddleware(
            displayErrorDetails: $this->config->isDisplayErrorDetails(),
            logErrors: $this->config->isLogErrors(),
            logErrorDetails: $this->config->isLogErrorDetails()
        );

        $errorHandler = new ExceptionHandler(
            $this->application->getCallableResolver(),
            $this->application->getResponseFactory()
        );

        $errorMiddleware->setDefaultErrorHandler($errorHandler);
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
            pattern: $this->jrpcConfig->getEntrypoint(),
            callable: $this->container->get(EntrypointController::class)
        );
    }
}