<?php

declare(strict_types=1);

namespace Kernel;

use Kernel\Command\BuiltinCommandDispatcher;
use Kernel\Command\BuiltinCommandRegistry;
use Kernel\Command\Interfaces\CommandDispatcher;
use Kernel\Command\Interfaces\CommandRegistry;
use Kernel\Config\ApplicationConfig;
use Kernel\Definitions\DependencyProvider;
use Kernel\Entrypoint\Entrypoint;
use Kernel\Entrypoint\EntrypointController;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use function DI\autowire;
use function DI\create;
use function DI\get;

final readonly class KernelDefinitions implements DependencyProvider
{
    public function register(): array
    {
        $definitions = [
            ApplicationConfig::class => autowire()->constructor($_ENV),
            ResponseFactoryInterface::class => create(Psr17Factory::class),
            TranslatorInterface::class => create(Translator::class)->constructor('RU-ru'),
            RuleHandlerResolverInterface::class => create(RuleHandlerContainer::class)
                ->constructor(
                    get(ContainerInterface::class)
                ),
            SerializerInterface::class => create(Serializer::class)
                ->constructor(
                    [
                        create(ObjectNormalizer::class),
                        create(ConstraintViolationListNormalizer::class)
                    ],
                    [get(EncoderInterface::class)]
                ),
            EncoderInterface::class => create(JsonEncoder::class),
            CommandRegistry::class => autowire(BuiltinCommandRegistry::class),
            CommandDispatcher::class => create(BuiltinCommandDispatcher::class)
                ->constructor(
                    get(CommandRegistry::class),
                    get(SerializerInterface::class),
                    get(ContainerInterface::class)
                )
        ];

        if ($_ENV['USE_DEFAULT_ENTRYPOINT']) {
            $definitions[EntrypointController::class] = autowire(Entrypoint::class);
        }

        return $definitions;
    }
}