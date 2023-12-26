<?php

declare(strict_types=1);

namespace Kernel;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Kernel\Configuration\ApplicationConfig;
use Kernel\Configuration\ConfigFactory;
use Kernel\Configuration\ConfigType;
use Kernel\Configuration\JsonRpcConfig;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Factory\AppFactory;
use Kernel\Definitions\DependencyCollector;
use Kernel\Environment\EnvLoader;

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
        EnvLoader::loadVariables();
        $configFactory = new ConfigFactory($_ENV);

        /** @var ApplicationConfig $config */
        $config = $configFactory->getConfig(ConfigType::Application);

        /** @var JsonRpcConfig $jrpcConfig */
        $jrpcConfig = $configFactory->getConfig(ConfigType::JsonRpc);

        $container = self::buildContainer($config, $jrpcConfig);
        $application = AppFactory::createFromContainer($container);

        $kernel = new Kernel($application, $config, $jrpcConfig, $container);
        return $kernel->setup();
    }

    /** @throws Exception */
    private static function buildContainer(ApplicationConfig $config, JsonRpcConfig $jrpcConfig): Container
    {
        $collector = new DependencyCollector($config, $jrpcConfig);
        $definitions = $collector->collect();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($definitions);

        return $containerBuilder->build();
    }
}