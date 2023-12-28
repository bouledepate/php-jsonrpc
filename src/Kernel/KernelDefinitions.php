<?php

declare(strict_types=1);

namespace JRPC\Kernel;

use JRPC\Kernel\Command\Builtin\DefaultCommandDispatcher;
use JRPC\Kernel\Command\Builtin\DefaultDTOFactory;
use JRPC\Kernel\Command\Builtin\DefaultCommandRegistry;
use JRPC\Kernel\Command\Builtin\DefaultDtoValidator;
use JRPC\Kernel\Command\Data\DtoCollectorInterface;
use JRPC\Kernel\Command\Data\DtoValidatorInterface;
use JRPC\Kernel\Command\Interfaces\CommandDispatcher;
use JRPC\Kernel\Command\Interfaces\CommandRegistry;
use JRPC\Kernel\Configuration\ApplicationConfig;
use JRPC\Kernel\Configuration\ConfigFactory;
use JRPC\Kernel\Configuration\ConfigType;
use JRPC\Kernel\Configuration\JsonRpcConfig;
use JRPC\Kernel\Definitions\DependencyProvider;
use JRPC\Kernel\Entrypoint\Entrypoint;
use JRPC\Kernel\Entrypoint\EntrypointController;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

final readonly class KernelDefinitions implements DependencyProvider
{
    public function __construct(private KernelConfig $config)
    {
    }

    public function register(): array
    {
        $definitions = [
            ConfigFactory::class => autowire()->constructor($_ENV),
            ApplicationConfig::class => factory([ConfigFactory::class, 'getConfig'])
                ->parameter('type', ConfigType::Application),
            JsonRpcConfig::class => factory([ConfigFactory::class, 'getConfig'])
                ->parameter('type', ConfigType::JsonRpc),
            ResponseFactoryInterface::class => create(Psr17Factory::class),
            CommandRegistry::class => autowire(DefaultCommandRegistry::class)
                ->constructor(
                    get(ApplicationConfig::class)
                ),
            CommandDispatcher::class => create(DefaultCommandDispatcher::class)
                ->constructor(
                    get(DefaultCommandRegistry::class),
                    get(DefaultDTOFactory::class),
                    get(SerializerInterface::class),
                    get(ContainerInterface::class)
                ),
            DtoCollectorInterface::class => create(DefaultDTOFactory::class)
                ->constructor(
                    get(SerializerInterface::class),
                    get(DtoValidatorInterface::class)
                ),
            DtoValidatorInterface::class => create(DefaultDtoValidator::class)
                ->constructor(
                    get(ValidatorInterface::class)
                ),
            ValidatorInterface::class => function () {
                return Validation::createValidatorBuilder()
                    ->enableAttributeMapping()
                    ->getValidator();
            },
            SerializerInterface::class => function () {
                $normalizers = [
                    new ObjectNormalizer(
                        nameConverter: new CamelCaseToSnakeCaseNameConverter(),
                        propertyTypeExtractor: new ReflectionExtractor()
                    ),
                    new ArrayDenormalizer(),
                    new DateTimeNormalizer(),
                    new BackedEnumNormalizer(),
                ];

                return new Serializer($normalizers, [new JsonEncoder()]);
            }
        ];

        if ($this->config->getJrpcConfig()->useDefaultEntrypoint()) {
            $definitions[EntrypointController::class] = autowire(Entrypoint::class);
        }

        return $definitions;
    }
}