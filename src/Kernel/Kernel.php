<?php

declare(strict_types=1);

namespace WoopLeague\Kernel;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\App;
use WoopLeague\Kernel\Config\ApplicationConfig;
use WoopLeague\Kernel\Config\EntrypointController;
use WoopLeague\Kernel\Error\ErrorHandler;
use WoopLeague\Kernel\Error\JsonRpc\ErrorRenderer as JsonRpcErrorRenderer;
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

        $errorMiddleware->setDefaultErrorHandler(new ErrorHandler(
            $this->application->getCallableResolver(),
            $this->application->getResponseFactory()
        ));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function applyEntrypoint(): void
    {
        $this->application->post(
            pattern: $this->config->getEntrypoint(),
            callable: $this->container->get(EntrypointController::class)
        );
    }
}