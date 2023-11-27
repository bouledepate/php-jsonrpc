<?php

declare(strict_types=1);

namespace WoopLeague\Kernel;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Factory\AppFactory;
use Kernel\Config\ApplicationConfig;
use Kernel\Definitions\DependencyCollector;
use Kernel\Helpers\EnvironmentHelper;

final readonly class KernelFactory
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws Exception
     */
    public static function buildApplication(): Kernel
    {
        EnvironmentHelper::loadVariables();
        $container = self::applyDependencies();
        return self::createKernel($container);
    }

    /** @throws Exception */
    private static function applyDependencies(): Container
    {
        $collector = new DependencyCollector();
        $definitions = $collector->collect();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($definitions);

        return $containerBuilder->build();
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    private static function createKernel(Container $container): Kernel
    {
        $application = AppFactory::createFromContainer($container);
        $kernel = new Kernel($application, $container->get(ApplicationConfig::class), $container);
        $kernel->setup();

        return $kernel;
    }
}