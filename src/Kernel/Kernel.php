<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Configuration\ApplicationConfig;
use Kernel\Configuration\JsonRpcConfig;
use Kernel\Entrypoint\EntrypointController;
use Kernel\Entrypoint\EntrypointNotSetException;
use Kernel\Exception\ExceptionHandler;
use Kernel\Middlewares\ComplianceMiddleware;
use Kernel\Middlewares\ContextMiddleware;
use Kernel\Middlewares\ValidationMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\App;

final readonly class Kernel
{
    public function __construct(
        private App                $application,
        private ApplicationConfig  $config,
        private JsonRpcConfig      $jrpcConfig,
        private ContainerInterface $container
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