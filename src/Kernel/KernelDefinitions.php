<?php

declare(strict_types=1);

namespace WoopLeague\Kernel;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use WoopLeague\Kernel\Command\CommandDispatcher;
use WoopLeague\Kernel\Command\CommandResolver;
use WoopLeague\Kernel\Config\ApplicationConfig;
use WoopLeague\Kernel\Config\DependencyProvider;

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
        return [
            ApplicationConfig::class => autowire()->constructor($_ENV),
            ResponseFactoryInterface::class => create(Psr17Factory::class),
            TranslatorInterface::class => create(Translator::class)->constructor('RU-ru'),
            RuleHandlerResolverInterface::class => create(RuleHandlerContainer::class)
                ->constructor(get(ContainerInterface::class)),
            CommandResolver::class => autowire(),
            CommandDispatcher::class => autowire(),
        ];
    }
}