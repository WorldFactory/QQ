<?php

namespace WorldFactory\QQ\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RunnerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('qq.factory.runners')) {
            return;
        }

        $definition = $container->findDefinition('qq.factory.runners');

        $taggedServices = $container->findTaggedServiceIds('qq.runner');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addRunnerDefinition', [$id, $attributes["type"], $attributes["alias"] ?? null, $attributes["deprecated"] ?? false]);
            }
        }
    }
}