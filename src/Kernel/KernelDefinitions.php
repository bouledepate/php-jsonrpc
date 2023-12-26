<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Command\BuiltinCommandDispatcher;
use Kernel\Command\BuiltinCommandDTOFactory;
use Kernel\Command\BuiltinCommandRegistry;
use Kernel\Command\Interfaces\CommandDispatcher;
use Kernel\Command\Interfaces\CommandDTOFactory;
use Kernel\Command\Interfaces\CommandRegistry;
use Kernel\Configuration\ApplicationConfig;
use Kernel\Configuration\ConfigFactory;
use Kernel\Configuration\ConfigType;
use Kernel\Configuration\JsonRpcConfig;
use Kernel\Definitions\DependencyProvider;
use Kernel\Entrypoint\Entrypoint;
use Kernel\Entrypoint\EntrypointController;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
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
    public function __construct(private JsonRpcConfig $jrpcConfig)
    {
    }

    public function register(): array
    {
        $definitions = [
            // Configurations
            ConfigFactory::class => autowire()->constructor($_ENV),
            ApplicationConfig::class => factory([ConfigFactory::class, 'getConfig'])
                ->parameter('type', ConfigType::Application),
            JsonRpcConfig::class => factory([ConfigFactory::class, 'getConfig'])
                ->parameter('type', ConfigType::JsonRpc),

            ResponseFactoryInterface::class => create(Psr17Factory::class),
            CommandRegistry::class => autowire(BuiltinCommandRegistry::class)
                ->constructor(
                    get(ApplicationConfig::class)
                ),
            CommandDispatcher::class => create(BuiltinCommandDispatcher::class)
                ->constructor(
                    get(CommandRegistry::class),
                    get(CommandDTOFactory::class),
                    get(SerializerInterface::class),
                    get(ContainerInterface::class)
                ),
            CommandDtoFactory::class => create(BuiltinCommandDTOFactory::class)
                ->constructor(
                    get(SerializerInterface::class),
                    get(ValidatorInterface::class)
                ),

            // Symfony definitions.
            // Validation component.
            ValidatorInterface::class => function () {
                return Validation::createValidatorBuilder()
                    ->enableAttributeMapping()
                    ->getValidator();
            },

            // Serializer component.
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

        if ($this->jrpcConfig->useDefaultEntrypoint()) {
            $definitions[EntrypointController::class] = autowire(Entrypoint::class);
        }

        return $definitions;
    }
}