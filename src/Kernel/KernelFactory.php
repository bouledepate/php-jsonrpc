<?php

declare(strict_types=1);

namespace JRPC\Kernel;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Configuration\ConfigFactory;
use JRPC\Kernel\Configuration\ConfigType;
use JRPC\Kernel\Configuration\JsonRpcConfig;
use JRPC\Kernel\Exception\InvalidMiddlewaresFormatException;
use JRPC\Kernel\Exception\MiddlewaresFileNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Factory\AppFactory;
use JRPC\Kernel\Definitions\DependencyCollector;
use JRPC\Kernel\Environment\EnvLoader;

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

        $configuration = self::collectConfiguration();
        $container = self::buildContainer($configuration);
        $application = AppFactory::createFromContainer($container);
        $middlewares = self::uploadMiddlewares($configuration);

        $kernel = new Kernel($application, $configuration, $container, $middlewares);
        return $kernel->setup();
    }

    /** @throws Exception */
    private static function buildContainer(KernelConfig $config): Container
    {
        $collector = new DependencyCollector($config);
        $definitions = $collector->collect();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($definitions);

        return $containerBuilder->build();
    }

    private static function collectConfiguration(): KernelConfig
    {
        $factory = new ConfigFactory($_ENV);

        /** @var ApplicationConfig $applicationConfig */
        $applicationConfig = $factory->getConfig(ConfigType::Application);

        /** @var JsonRpcConfig $jrpcConfig */
        $jrpcConfig = $factory->getConfig(ConfigType::JsonRpc);

        return new KernelConfig(
            mainConfig: $applicationConfig,
            jrpcConfig: $jrpcConfig
        );
    }

    /**
     * @throws InvalidMiddlewaresFormatException
     * @throws MiddlewaresFileNotFoundException
     */
    private static function uploadMiddlewares(KernelConfig $config): array
    {
        $middlewares = [];
        $mainConfig = $config->getMainConfig();

        if (null === $mainConfig->getMiddlewaresDirectory()) {
            return $middlewares;
        }

        $middlewaresPath = dirname(__DIR__, 2) . $mainConfig->getMiddlewaresDirectory();
        if (false === file_exists($middlewaresPath)) {
            throw new MiddlewaresFileNotFoundException($middlewaresPath);
        }

        $middlewares = require_once $middlewaresPath;
        if (false === is_array($middlewares)) {
            throw new InvalidMiddlewaresFormatException($middlewaresPath);
        }

        return $middlewares;
    }
}