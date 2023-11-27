<?php

declare(strict_types=1);

namespace WoopLeague\Kernel;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Middleware\ErrorMiddleware;
use WoopLeague\Kernel\Config\ApplicationConfig;
use WoopLeague\Kernel\Entrypoint\EntrypointController;
use WoopLeague\Kernel\Entrypoint\EntrypointNotSetException;
use WoopLeague\Kernel\Error\ErrorHandler;
use WoopLeague\Kernel\Error\ShutdownErrorHandler;
use WoopLeague\Kernel\Middlewares\JsonRPC\ComplianceMiddleware;
use WoopLeague\Kernel\Middlewares\JsonRPC\ContextMiddleware;
use WoopLeague\Kernel\Middlewares\JsonRPC\ValidationMiddleware;

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
        $this->registerErrorHandlers($errorMiddleware);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

    private function registerErrorHandlers(ErrorMiddleware $errorMiddleware): void
    {
        $errorHandler = new ErrorHandler(
            $this->application->getCallableResolver(),
            $this->application->getResponseFactory()
        );

        $requestCreator = ServerRequestCreatorFactory::create();
        $shutdownHandler = new ShutdownErrorHandler(
            request: $requestCreator->createServerRequestFromGlobals(),
            errorHandler: $errorHandler,
            displayErrorDetails: $this->config->isDisplayErrorDetails()
        );

        $errorMiddleware->setDefaultErrorHandler($errorHandler);
        register_shutdown_function($shutdownHandler);
    }

}