<?php

declare(strict_types=1);

namespace Bouledepate\JsonRpc;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;

/**
 * @package Bouledepate\JsonRpc
 * @author Semyon Shmik <promtheus815@gmail.com>
 */
abstract class DefaultMiddleware implements MiddlewareInterface
{
    public function __construct(protected readonly ContainerInterface $container)
    {
    }

    /**
     * Retrieves an instance from the container or returns a default value.
     *
     * @param string $interface The interface or class name to retrieve.
     * @param mixed|null $default The default value to return if the instance is not found.
     *
     * @return mixed The instance retrieved from the container or the default value.
     *
     * @throws ContainerExceptionInterface If there is an error retrieving the instance.
     * @throws NotFoundExceptionInterface If the interface is not found in the container.
     */
    protected function getContainerInstance(string $interface, mixed $default = null): mixed
    {
        if ($this->container->has($interface)) {
            $instance = $this->container->get($interface);
            if ($instance instanceof $interface) {
                return $instance;
            }
        }
        return $default;
    }

    /**
     * Retrieves the ResponseFactoryInterface instance from the container.
     *
     * @return ResponseFactoryInterface The response factory.
     *
     * @throws ContainerExceptionInterface If there is an error retrieving the instance.
     * @throws NotFoundExceptionInterface If the ResponseFactoryInterface is not found in the container.
     */
    protected function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->getContainerInstance(ResponseFactoryInterface::class)
            ?? throw new RuntimeException('An instance of ResponseFactoryInterface must be provided.');
    }
}