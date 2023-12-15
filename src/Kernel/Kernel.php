<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Config\ApplicationConfig;
use Kernel\Entrypoint\EntrypointController;
use Kernel\Entrypoint\EntrypointNotSetException;
use Kernel\Error\ErrorHandler;
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
        private ContainerInterface $container
    )
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws EntrypointNotSetException
     */
    public function setup(): void
    {
        $this->connectMiddlewares();
        $this->applyEntrypoint();
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
        $this->application->add($this->container->get(ComplianceMiddleware::class));
        $this->application->add($this->container->get(ValidationMiddleware::class));
        $this->application->add($this->container->get(ContextMiddleware::class));

        $this->application->addBodyParsingMiddleware();

        $errorMiddleware = $this->application->addErrorMiddleware(
            displayErrorDetails: $this->config->isDisplayErrorDetails(),
            logErrors: $this->config->isLogErrors(),
            logErrorDetails: $this->config->isLogErrorDetails()
        );
        $errorHandler = new ErrorHandler(
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
        if ($this->container->has(EntrypointController::class) === false) {
            throw new EntrypointNotSetException();
        }

        $this->application->post(
            pattern: $this->config->getEntrypoint(),
            callable: $this->container->get(EntrypointController::class)
        );
    }
}